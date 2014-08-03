<?php
/**
 * Zaboy_Dic
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Dic/Abstract.php';
  require_once 'Zaboy/Dic/Interface.php';
  
/**
 * Zaboy_Dic
 * 
 * @see Zaboy_Dic_Interface
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic extends Zaboy_Dic_Abstract implements Zaboy_Dic_Interface
{
    /*
     * array of services which are starting for check loop depends
     */
    private $_runningServices = array();

    /*
     * array of services which are already loaded
     * array( 'serviceName' => intance )
     */
    private $_loadedServices = array();

    /**
    * 
    * @see $_options
    * param array <b>options</b> - options from config.ini. <br>It is all after  <i>resources.dic.</i>
    * @return void
    */  
    public function __construct( array $options=array() ) 
    {        
        parent::__construct($options);
        $this->_autoloadServices();
        return;
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
     * About Zaboy_Dic see {@see Zaboy_Dic_Interface}<br>
     * also see {@see Zaboy_Dic_Interface::get()}
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
        /* @var $result Zaboy_Dic_Service_Interface */
        if (method_exists($result, 'setServiceName')) {//is_callable(array($result, 'setServiceName'))
            $result->setServiceName($serviceName);
        }    
        //protection from loop of calls
        $this->_uncheckRuning($serviceName);        
        return $result;
     }
     
     /**
      * @param string $serviceName
      * @param string $serviceClass
      * @param \ReflectionClass $reflectionObject
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

        $isCallParamService = is_a( $callParamInfo['paramClass'], 'Zaboy_Dic_Service_Interface' , true ); //is it Service?
        $isCallParamServiceInConfig = key_exists( $callParamInfo['paramName'] , $this->_servicesConfig ); 
        $isCallParamOptional = $callParamInfo['isOptional'];
        if ( $isCallParamService && ( !$isCallParamOptional || $isCallParamServiceInConfig) ) {//is it service not optional or present in config?
            $value = $this->get($callParamInfo['paramName'], $callParamInfo['paramClass']);
            return $value;
        } 
        
        //add search info about param in: $_egisterdParams (from config.ini), Resurses, ZendRegistry
        if ($callParamInfo['isOptional']) { return null;}
        
        require_once 'Avz/Exception.php';
        throw new Zaboy_Exception('Param ' . $callParamInfo['paramName'] . ' in ' .  $serviceName . ':: _consruct() is not resolved'); 
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
             throw new Zaboy_Exception("Loop in calls load Service($serviceName)"); 
         }
         $this->_runningServices[$serviceName]= $serviceName;         
    }
     
     /**
      * 
      * @param string $serviceName
      * @return void
      */
    protected function _uncheckRuning($serviceName) {
         unset($this->_runningServices[$serviceName]);      
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
    
    /**
     * information about class from config is more important
     * 
     * @todo Make control for situation, when class in config parent of $serviceClass or not compatible 
     * @param string $serviceName
     * @param string $serviceClass
     * @return string
     */
    private function _resolveServiceClass($serviceName,$serviceClass = null ) {
         if (key_exists( $serviceName , $this->_servicesConfig ) && key_exists( self::CONFIG_KEY_CLASS, $this->_servicesConfig[$serviceName] )) {
             $serviceClass =  $this->_servicesConfig[$serviceName][self::CONFIG_KEY_CLASS];
         }   //information about class from config is more important^^^
         
        if ( !isset($serviceClass) ){ 
             require_once 'Avz/Exception.php';
             throw new Zaboy_Exception("Can't resolve class name fore service $serviceName"); 
        }
        
        return $serviceClass;
    }
    
    /**
     * Class must be load if resources.dic.service.WithAutoload.autoload = true, where 'WithAutoload' is services name
     *  - in config          resources.dic.service.WithAutoload.autoload = true
     * 
     * @return void
     */
    private function _autoloadServices() {
        foreach ($this->_servicesConfig as $serviceName => $serviceConfig ) {
            if (key_exists( self::CONFIG_KEY_AUTOLOAD , $serviceConfig )&& (bool)$serviceConfig[self::CONFIG_KEY_AUTOLOAD]) {
                $this->get($serviceName);
            }
        }
        return ;
    }
    
}