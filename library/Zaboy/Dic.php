<?php
/**
 * Zaboy_Dic
 * 
 * @category   Widgets
 * @package    Widgets
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Dic/Abstract.php';
  require_once 'Zaboy/Dic/Interface.php';
  
/**
 * Zaboy_Dic
 * 
 * @see Zaboy_Dic_Interface
 * @category   Widgets
 * @package    Widgets
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic extends Zaboy_Dic_Abstract implements Zaboy_Dic_Interface
{
     /**
     * comfig.ini :  
     * dic.service.serviceName.class = Same_Class
     * dic.service.serviceName.options.key = val 
     */
     const CONFIG_KEY_SERVICE = 'service';
    
    /*
     * @var Zaboy_Dic_ServicesConfigs
     */
    private $_servicesConfigs = array();

    /*
     * Object helps chekc loops in dependencies in the tree of services constructors
     * 
     * Zaboy_Dic_LoopChecker
     */
    private $_loopChecker;
    
 
    /*
     * Object contane information about loaded Services
     * 
     * Zaboy_Dic_ServicesStore
     */
    private $_servicesStore;   

    /**
    * 
    * @see $_options
    * param array <b>options</b> - options from application.ini. <br>It is all after  <i>resources.dic.</i>
    * @return void
    */  
    public function __construct( array $options=array() ) 
    {
        $this->_servicesConfigs = new Zaboy_Dic_ServicesConfigs($this);
        if (isset($options[self::CONFIG_KEY_SERVICE])) {
            $this->_servicesConfigs->setConfigsServices($options[self::CONFIG_KEY_SERVICE]);
            unset($options[self::CONFIG_KEY_SERVICE]);        
        }
        $this->_loopChecker = Zaboy_Dic_LoopChecker();
        $this->_servicesStore = new Zaboy_Dic_ServicesStore();
        parent::__construct($options);
        $this->_servicesConfigs->autoloadServices();
    }    
    
    /**
      * @param string
      * @return bool
      */    
    public function has($serviceName)
    {
        return key_exists($serviceName, $this->_loadedServices);
    }
    
      /**
      * Return class by service name
       * 
       * return class of loaded service if it is already loaded or
       * if it isn't loaded, but if it is described ( for example in config.ini) -
       * return class from description
      * else return null
      * 
      * @param string
      * @return string Class of service or null
      */    
    public function getClass($serviceName)
    {
        if  (key_exists($serviceName, $this->_loadedServices)) {
            $serviceClass = get_class($this->_loadedServices[$serviceName]); //if is already loaded
        }else{         
            if (key_exists( $serviceName , $this->_servicesConfig ) && key_exists( self::CONFIG_KEY_CLASS, $this->_servicesConfig[$serviceName] )) {
                $serviceClass =  $this->_servicesConfig[$serviceName][self::CONFIG_KEY_CLASS]; //if isn't loaded, but is described ( for example in config.ini)
            } else {
                $serviceClass = null;
            }
        }
        return $serviceClass;        
    }
    
    /**
     * 
     * Class in config is more important then class in __constract
     * 
     * About Avz_Dic see {@see Avz_Dic_Interface}<br>
     * also see {@see Avz_Dic_Interface::get()}
     * 
    * @see http://stackoverflow.com/questions/1935771/how-to-use-call-user-func-array-with-an-object-with-a-construct-method-in-php
    * @param string
    * @param string
    * @return object
    */    
    public function get($serviceName,$serviceClass = null)
    {
        if (key_exists($serviceName, $this->_loadedServices)) {
            return $this->_loadedServices[$serviceName];
        }
        //protection from loop of calls
        $this->_checkRuning($serviceName);
        
        $serviceClass = $this->_resolveServiceClass($serviceName,$serviceClass );
        $reflectionObject = new ReflectionClass($serviceClass);
        $callParamsArray = $this->_getCallParameters($serviceName,$serviceClass, $reflectionObject); //array ( int => value)
        $result = $reflectionObject->newInstanceArgs($callParamsArray); // it' like new class($callParamsArray[1], $callParamsArray[2]...)
        
        $this->_loadedServices[$serviceName] = $result;
        /* @var $result Avz_Dic_Service_Interface */
        //protection from loop of calls
        $this->_uncheckRuning($serviceName);        
        return $result;
     }
     
     /**
      * @param \ReflectionClass $reflectionObject
      * @param string $serviceName
      * @param string $serviceClass
      * @return array   array( int => value)
      */
     protected function _getCallParameters($serviceName, $serviceClass, \ReflectionClass $reflectionObject) {
        $callParamsArray = array(); //array( int => value)

        //Get params for $className::__construct
        $reflectionConstruct = $reflectionObject->getMethod('__construct');
        $reflectionParams = $reflectionConstruct->getParameters();

        foreach ($reflectionParams as $callParam) {
        /** @var $callParam \ReflectionParameter  */ 
             $callParamsArray[] = $this->_getParamValue($serviceName, $callParam);
         }
        //all prams are ready in $callParamsArray
         return $callParamsArray;
     }
     
    /**
     * @todo add search info about param in: $_egisterdParams (from config.ini), Resurses, ZendRegistry
     * @param string $serviceName
     * @param \ReflectionParameter $callParam
     * @return mix
     */
    protected function _getParamValue($serviceName, \ReflectionParameter  $callParam) {

        $callParamInfo = $this->_getParamInfoFromReflection($callParam); //array 'paramPosition','paramName', 'paramClass', 'isOptional'

        $value = $this->_resolveOptions($serviceName, $callParamInfo);
        if (isset($value)) { return $value; }

        $isCallParamService = is_a( $callParamInfo['paramClass'], 'Avz_Dic_Service_Interface' , true ); //is it Service?
        $isCallParamServiceInConfig = key_exists( $callParamInfo['paramName'] , $this->_servicesConfig ); 
        $isCallParamOptional = $callParamInfo['isOptional'];
        if ( $isCallParamService && ( !$isCallParamOptional || $isCallParamServiceInConfig) ) {//is it service not optional or present in config?
            $value = $this->get($callParamInfo['paramName'], $callParamInfo['paramClass']);
            return $value;
        } 
        
        //add search info about param in: $_egisterdParams (from config.ini), Resurses, ZendRegistry
        if ($callParamInfo['isOptional']) { return null;}
        
        require_once 'Avz/Exception.php';
        throw new Avz_Exception('Param ' . $callParamInfo['paramName'] . ' in ' .  $serviceName . ':: _consruct() is not resolved'); 
    }    
    
    /**
     * @param string $serviceName
     * @param array $callParamInfo 
     */
    protected function _resolveOptions($serviceName, $callParamInfo) {
        $callParamName = $callParamInfo['paramName'];
        if ($callParamName == 'options') {
            if (key_exists( $serviceName , $this->_servicesConfig ) && key_exists( self::CONFIG_KEY_OPTIONS, $this->_servicesConfig[$serviceName] )) {
                $options = $this->_servicesConfig[$serviceName][self::CONFIG_KEY_OPTIONS];
                return $options;
            }
            return array();
        }
        return null;
     }    
     
     /**
      * 
      * @param string $serviceName
      * @return bool
      */
     protected function _checkRuning($serviceName) {
         if (key_exists($serviceName, $this->_runningServices)) {
             require_once 'Avz/Exception.php';
             throw new Avz_Exception("Loop in calls load Service($serviceName)"); 
         }
         $this->_runningServices[$serviceName]= $serviceName;         
    }
    
    /**
     * 
     * @param \ReflectionParameter $callParam
     * @return array
     */
    private function _getParamInfoFromReflection( \ReflectionParameter $callParam) {
        $callParamInfo = array();
        $callParamInfo['paramPosition'] = $callParam->getPosition();
        $callParamInfo['paramName'] = $callParam ->getName();
        if ($callParam->getClass()!== null) {
             $callParamInfo['paramClass'] = $callParam->getClass()->getName();
        }
        $callParamInfo['isOptional'] = $callParam->isOptional();
        return $callParamInfo;
    }
    
}