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
    public function __construct
    ( 
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
     * Return Service if is described or just object with injected dependencies
     * 
     * @param string $name
     * @param string $class
     * @return object|null
     */
    public function get($name, $class = null)    
    {
        if (!$this->_servicesConfigs->isService($name)) {
            $serviceObject = getService($name); 
            return $serviceObject;
        }else{
            // $name is not Service name            
            return $this->_createServiceObject($name, $class);            
        }
    }
    
    /**
     * instantiate service
     * 
     * @param type $serviceName
     * @return null
     */
    public function getService($serviceName)    
    {
        if (!$this->_servicesConfigs->isService($serviceName)) {
            return null;
        }   
        
        $initiation = $this->_servicesConfigs->getServiceInitiation($serviceName);
        switch ($initiation) {
            case Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_SINGLETON:
                // Is Service with $serviceName already was loaded?
                if ($this->_servicesStore->hasService($serviceName)) {
                    $serviceObject = $this->_servicesStore->getService($serviceName);
                }else{
                    $serviceObject = $this->_createServiceObject($serviceName);
                    $this->_servicesStore->addService($serviceName, $serviceObject);
                }
                break;
            case Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_RECREATE :
                $serviceObject = $this->_createServiceObject($serviceName);
                $this->_servicesStore->addService($serviceName, $serviceObject);
                break;
            case Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_CLONE :
                // Is etalon Service for clone already was loaded?
                $etalonObject = $this->_servicesStore->getEtalon($serviceName);
                if (!isset($etalonObject)) {
                    $etalonObject = $this->_createServiceObject($serviceName);
                    $this->_servicesStore->addEtalon($serviceName, $etalonObject);
                }
                $serviceObject = clone $etalonObject;
                $this->_servicesStore->addService($serviceName, $serviceObject);
                break;
            default:
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception(
                    'unknow initiation type - ' . $initiation
                ); 
        }
      
        return $serviceObject;
    }    
    
      /**
       * Return Service Name for Service Object
       * 
       * @param object $objectInstance usually $this
       * @return string Service Name
       */
    public function getServiceName($objectInstance) 
    {
        $servicesArray = $this->_servicesStore->getServices();
        foreach ($servicesArray as $serviceName => $serviceObjectFromStore) {
            if ($objectInstance === $serviceObjectFromStore) {
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
    public function _get($serviceName,$serviceClassFromParam = null)
    {
    }

    /**
     * @param string $objectName
     * @param string $objectClass
     * @return object
     */
    protected function _createServiceObject($objectName, $objectClass = null) 
    {
        if (!$this->_servicesConfigs->isService($objectName)) {
            $objectClass = $this->_servicesConfigs->getServiceClass($objectName);
        }else{
            if (!isset($objectClass)) {
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception(
                    'can not resolve class for - ' . $objectName
                ); 
            }    
        }
       //protection from loop of calls
       $this->_loopChecker->loadingStart($objectName);
       $reflectionObject = new ReflectionClass($objectClass);
       $callParamsArray = $this->_getConstructParamsValues($objectName, $reflectionObject);
       $objectInstance = $reflectionObject->newInstanceArgs($callParamsArray); // it's like new class($callParamsArray[1], $callParamsArray[2]...)
       $this->_loopChecker->loadingFinished($objectName);

       return $objectInstance;
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
                // optional params don't load
                $serviceObject = null;
            }else{
                $constructParamName = $reflectionParam->getName();
                $serviceForConstructParam = $this->_servicesConfigs
                    ->getServiceForConstructParam($serviceName, $constructParamName);
                if (isset($serviceForConstructParam)) {
                    $serviceObject = $this->getService($serviceForConstructParam);
                }else{
                    $constructParamClassObject = $reflectionParam->getClass();
                    if (isset($constructParamClassObject)) {
                        $constructParamClass = $constructParamClassObject->getName();
                    }else{
                        $constructParamClass = null;
                    }
                }
                
                $serviceObject = $this->get($constructParamName, $constructParamClass);                
            }
            $constructParams[] = $serviceObject;
        }
        return $constructParams;
    } 

}