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
  require_once 'Zaboy/Dic/ServicesConfigs.php';
  require_once 'Zaboy/Dic/LoopChecker.php';
  require_once 'Zaboy/Dic/ServicesStore.php';
  
/**
 * Zaboy_Dic
 * 
 * {@inheritdoc}
 * 
 * @see Zaboy_Dic_Interface
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic extends Zaboy_Dic_Abstract
{
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
    
    /**
    * 
    * @see $_options
    * param array <b>options</b> - options from application.ini. <br>It is all after  <i>resources.dic.</i>
    * @return void
    */  
    public function __construct( 
        array $options, 
        Zaboy_Dic_ServicesConfigs $servicesConfigs,
        Zaboy_Dic_ServicesStore $servicesStore,
        Zaboy_Dic_LoopChecker $loopChecker        
    ){
        parent::__construct($options);     
        $this->_servicesConfigs = $servicesConfigs;
        $this->_servicesStore = $servicesStore;        
        $this->_loopChecker = $loopChecker;
        
        $this->_servicesConfigs->autoloadServices();
    }  
 
     /**
       * Return Service Name for Service Object
       * 
       * @param object $serviceObjectFromParams usually $this
       * @return string Service Name
       */
      public function getServiceName($serviceObjectFromParams) 
    {
        $servicesArray = $this->_servicesStore->getServices();
        foreach ($servicesArray as $serviceName => $serviceObjectFromStore) {
            if ($serviceObjectFromParams === $serviceObjectFromStore) {
                return $serviceName;
            }
        }
        return null;       
     }   
    
    /**
     * Class in config is more important then class in __constract
     * 
     * About Avz_Dic see {@see Avz_Dic_Interface}<br>
     * also see {@see Avz_Dic_Interface::get()}<br><br>
     * {@inheritdoc}
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
        if (null !== $this->_servicesConfigs->getServiceClass($serviceName)) {
            $serviceClass = $this->_servicesConfigs->getServiceClass($serviceName);
            $serviceObject = $this->_loadServiceObject($serviceName, $serviceClass);
            return $serviceObject;
        }
        if (!isset($serviceClassFromParam)) {
            require_once 'Zaboy/Dic/Exception.php';
            throw new Zaboy_Dic_Exception("Cann't resolve Service Class for ($serviceName)");            
        }    
        if ($serviceClassFromParam === self::IT_IS_OPTIONAL) {
            return null;
        }    
        $serviceObject = $this->_loadServiceObject($serviceName, $serviceClassFromParam);
        return $serviceObject;
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
       $callParamsArray = $this->_getConstructParamsValues($serviceName, $reflectionObject);
       $serviceObject = $reflectionObject->newInstanceArgs($callParamsArray); // it's like new class($callParamsArray[1], $callParamsArray[2]...)
       $this->_loopChecker->loadingFinished($serviceName);
       $this->_servicesStore->addService($serviceName, $serviceObject);
       return $serviceObject;
    }

     /**
     * @param string 
     * @param /ReflectionObject
     * @return array  {@see ReflectionClass::newInstanceArgs()}
     */
    protected function _getConstructParamsValues($serviceName, $reflectionObject)
    {
        //Get params for $className::__construct
        $reflectionConstruct = $reflectionObject->getMethod('__construct');
        $reflectionParams = $reflectionConstruct->getParameters();
        $constructParams = array();
        //extract option (if exist) from array $reflectionParams
        if (isset($reflectionParams[0])) {
            $mayBeOptionsParam = $reflectionParams[0];
            /** @var $mayBeOptionsParam \ReflectionParameter  */
            $mayBeOptionsParamName = $mayBeOptionsParam->getName();
            if ($mayBeOptionsParamName === 'options') {
                $optionsFromConfig = $this->_servicesConfigs->getServiceOptions($serviceName);
                $constructParams[] = $optionsFromConfig;
                array_shift($reflectionParams);               
            }           
        }
        foreach ($reflectionParams as $reflectionParam) {
            /** @var $reflectionParam \ReflectionParameter  */
            if ($reflectionParam->isOptional()) {
                $constructParamClass = self::IT_IS_OPTIONAL;
            }else{
                $constructParamClassObject = $reflectionParam->getClass();
                if (isset($constructParamClassObject)) {
                    $constructParamClass = $constructParamClassObject->getName();
                }else{
                    $constructParamClass = null;
                }
            }
            $constructParamName = $reflectionParam->getName();
            $serviceObject = $this->get($constructParamName, $constructParamClass);
            $constructParams[] = $serviceObject;
        }
        return $constructParams;
    } 

}