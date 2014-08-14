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
     
    /**
     * If dependent  Service ( which is specifed in __construct parameters) is optional
     * it will be loaded only if it was described  in config.
     * But if that optional Service is already loaded - it will be used
     */
     const IT_IS_OPTIONAL = 'this parameter is optional';    
    
    /*
     * @var Zaboy_Dic_ServicesConfigs
     */
    private $_servicesConfigs;

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
 
    /*
     * Object resolve information about parametrs of construct Servces
     * 
     * Zaboy_Dic_ServiceReflector
     */
    private $_serviceReflector;
    
    /**
    * 
    * @see $_options
    * param array <b>options</b> - options from application.ini. <br>It is all after  <i>resources.dic.</i>
    * @return void
    */  
    public function __construct( array $options=array() ) 
    {
        parent::__construct($options);      
        $this->_servicesConfigs = new Zaboy_Dic_ServicesConfigs($this);
        $this->_servicesConfigs->setConfigsServices($this->getAttrib(self::CONFIG_KEY_SERVICE));
        $this->removeAttrib(self::CONFIG_KEY_SERVICE);
        
        $this->_loopChecker = new Zaboy_Dic_LoopChecker();
        $this->_servicesStore = new Zaboy_Dic_ServicesStore();
        $this->_serviceReflector = new Zaboy_Dic_ServiceReflector();       

        $this->_servicesConfigs->autoloadServices();
    }    
    
    /**
     * Class in config is more important then class in __constract
     * 
     * About Avz_Dic see {@see Avz_Dic_Interface}<br>
     * also see {@see Avz_Dic_Interface::get()}
     * 
     * @see http://stackoverflow.com/questions/1935771/how-to-use-call-user-func-array-with-an-object-with-a-construct-method-in-php
     * @param string Service Name
     * @param string Sevice Class. Use if Service isn't load and haven't config. Try don't use it.
     * @return object
     */    
    public function get($serviceName,$serviceClassFromParam = null)
    {
        if ($this->_servicesStore->hasService($serviceName)) {
            return $this->_servicesStore->getService($serviceName);
        }
        if (isset($this->_servicesConfigs->getServiceClass($serviceName))) {
            $serviceClass = $this->_servicesConfigs->getServiceClass($serviceName);
            $serviceObject = $this->_loadServiceObject($serviceName, $serviceClass);
            return $serviceObject;
        }
        if (isset($serviceClassFromParam)) {
            if ($serviceClassFromParam === self::IT_IS_OPTIONAL) {
                 return null;
            }else{
                $serviceObject = $this->_loadServiceObject($serviceName, $serviceClassFromParam);
                return $serviceObject;
            }
        }
        require_once 'Zaboy/Dic/Exception.php';
        throw new Zaboy_Dic_Exception("Cann't resolve Service Class for ($serviceName)");
    }

     /**
      * @param string $serviceName
      * @param string $serviceClass
      * @return object
      */
    protected function _loadServiceObject($serviceName, $serviceClass) 
    {
       //protection from loop of calls
       $this->_loopChecker->loadingStart($serviceName);
       $reflectionObject = new ReflectionClass($serviceClass);
       $callParamsArray = $this->_serviceReflector->getConstructParamsValues($serviceName, $reflectionObject);
       $serviceObject = $reflectionObject->newInstanceArgs($callParamsArray); // it' like new class($callParamsArray[1], $callParamsArray[2]...)
       $this->_loopChecker->loadingFinished($serviceName);
       $this->_servicesStore->addService($serviceName, $serviceObject);
       return $serviceObject;
    }

     /**
     * @param string 
     * @param string /ReflectionObject
     * @param array $callParamInfo 
     */
    public function getConstructParamsValues($serviceName, $reflectionObject)
    {
        //Get params for $className::__construct
        $reflectionConstruct = $reflectionObject->getMethod('__construct');
        $reflectionParams = $reflectionConstruct->getParameters();
        //extract option (if exist) from array $reflectionParams
        if (isset($reflectionParams[0])) {
            $mayBeOptionsParam = $reflectionParams[0];
            /** @var $mayBeOptionsParam \ReflectionParameter  */
            $mayBeOptionsParamName = $mayBeOptionsParam->getName();
            if ($mayBeOptionsParamName === 'options') {
                $optionsFromConfig = $this->_servicesConfigs->getServiceOptions($serviceName);
                $constructParams = $optionsFromConfig;
                array_shift($reflectionParams);               
            }           
        }
        foreach ($reflectionParams as $reflectionParam) {
            /** @var $reflectionParam \ReflectionParameter  */
            if ($reflectionParams->isOptional()) {
                $constructParamClass = self::IT_IS_OPTIONAL;
            }else{
                $constructParamClass = $reflectionParams->getClass()->getName();               
            }
            $constructParamName = $reflectionParam->getName();
            $serviceObject = $this->get($constructParamName, $constructParamClass);
            $constructParams[] = $serviceObject;
        }
        return $constructParams;
    } 
    
}