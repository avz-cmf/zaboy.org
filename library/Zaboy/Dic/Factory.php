<?php
/**
 * Zaboy_Dic_Factory
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  require_once 'Zaboy/Dic/ServicesConfigs.php';
  require_once 'Zaboy/Dic/LoopChecker.php';
  
/**
 * This is factory for "make" service and objects which are created {@see Zaboy_Dic}
 * 
 * There is special method {@see getOptionalParamValue()} for lazy load.
 * See  {@see Zaboy_Dic::getOptionalParamValue()} and 
 * magic methods in {@see Zaboy_Services}. 
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_Factory
{
    
    /*
     * @var Zaboy_Dic_ServicesConfigs
     */
    private $_servicesConfigs;

    /*
     * Object contane information about loaded Services
     * 
     * Zaboy_Dic_ServicesStore
     */
    private $_servicesStore;   

    /*
     * Object helps check loops in dependencies in the tree of services constructors
     * 
     * @var Zaboy_Dic_LoopChecker
     */
    private $_loopChecker;
    
    /**
     * Constructor - I hope you know what is this
     * 
     * @param Zaboy_Dic_ServicesConfigs $servicesConfigs
     * @param Zaboy_Dic_ServicesStore $servicesStore
     * @param Zaboy_Dic_LoopChecker $loopChecker
     */
    public function __construct( 
        Zaboy_Dic_ServicesConfigs $servicesConfigs,
        Zaboy_Dic_ServicesStore $servicesStore,
        Zaboy_Dic_LoopChecker $loopChecker        
    ){
        $this->_servicesConfigs = $servicesConfigs;
        $this->_servicesStore = $servicesStore;
        $this->_loopChecker = $loopChecker;
    }  

    /**
     * Instantiate Service
     * 
     * @param string $serviceName
     * @return void
     */
    public function createService($serviceName)    
    {
        $serviceClass = $this->_servicesConfigs->getServiceClass($serviceName);
        if (empty($serviceClass)) {
                    require_once 'Zaboy/Dic/Exception.php';
                    throw new Zaboy_Dic_Exception(
                        "Class isn't defined in Service Config for Service: " . $serviceName);
        }
        $serviceInstance = $this->_create($serviceClass, $serviceName); 
        return $serviceInstance;            
    }    

    /**
     * Instantiate Object
     * 
     * @param string $objectClass
     * @return void
     */
    public function createObject($objectClass)    
    {
        $objectInstance = $this->_create($objectClass);
        return $objectInstance;
    }
    
    /**
     * Instantiate Object for param by $paramName in $reflectionClass
     * 
     * Don't work with $paramName = 'options'
     * It is used Dic::getOptionalParamValue()
     * 
     * @param string $paramName
     * @param /ReflectionClass $reflectionClass
     * @param string $serviceName
     * @return object
     */
    public function getOptionalParamValue($paramName, $reflectionClass, $serviceName = null)    
    {
        //Get params for $className::__construct
        $reflectionConstruct = $this->_getReflectionConstruct( $reflectionClass );
        /* @var $reflectionConstruct /ReflectionMethod */
        if (!is_null($reflectionConstruct)) {
            $reflectionParams = $reflectionConstruct->getParameters();
            // $reflectionParams array of ReflectionParameter
            foreach ($reflectionParams as $reflectionParam) {
                /* @var $reflectionParam /ReflectionParameter */
                if ($reflectionParam->isOptional() && $reflectionParam->getName() === $paramName ) {
                    $constructParamValue = $this
                        ->_getConstructParamValue($reflectionParam, $serviceName);
                    return $constructParamValue;   
                }    
            }        
        }
        require_once 'Zaboy/Dic/Exception.php';
        throw new Zaboy_Dic_Exception(
            "There isn't param  $paramName in class " . $reflectionClass->getName());        
    }
    
    /**
     * 
     * @param string $className
     * @param string $serviceName
     * @return object
     */
    public function _create($className, $serviceName = null)    
    {
        if (is_null($serviceName)) {
            $loopCheckId = $className;    
        }else{
            $loopCheckId = $serviceName;            
        }
        $this->_loopChecker->loadingStart($loopCheckId);
        $reflectionClass = new ReflectionClass($className);  
        /* @var $reflectionClass /ReflectionClass */
        $callParamsArray = $this->_getConstructParamsValues( $reflectionClass, $serviceName);
        // it's like new class($callParamsArray[1], $callParamsArray[2]...)              
        $objectInstance = $reflectionClass->newInstanceArgs($callParamsArray); 
        $this->_loopChecker->loadingFinished($loopCheckId);        
        return $objectInstance;
    }

    /**
     * Get ( or make) all params for _constructor of class which $reflectionClass was taken
     * 
     * If it is Service, ($serviceName isn't null) information from service config
     * will be used
     * 
     * @param /ReflectionClass $reflectionClass
     * @param string|null $serviceName
     * @return array  params for /ReflectionClass::newInstanceArgs()
     */
    protected function _getConstructParamsValues( $reflectionClass, $serviceName= null)
    {
        $constructParams = array();
         //Get params for $className::__construct
        $reflectionConstruct = $this->_getReflectionConstruct( $reflectionClass );
        /* @var $reflectionConstruct /ReflectionMethod */
        if (!is_null($reflectionConstruct)) {
            $reflectionParams = $reflectionConstruct->getParameters();
            // $reflectionParams array of ReflectionParameter
            $options = $this->_resolveOptions($reflectionParams, $serviceName);
            if (isset($options)) {
                $constructParams[] = $options;
                array_shift($reflectionParams);
            }            
            foreach ($reflectionParams as $reflectionParam) {
                if ($reflectionParam->isOptional()) {
                    // optional params isn't loaded
                    $constructParams[] = null;
                }else{
                    $constructParams[] = $this
                        ->_getConstructParamValue($reflectionParam, $serviceName);
                }    
            }
        }
        return $constructParams;
    } 

    /**
     * 
     * @param /ReflectionClass $reflectionClass
     * @return /ReflectionMethod
     */
    protected function _getReflectionConstruct( ReflectionClass $reflectionClass )    
    {
        $reflectionMethods = $reflectionClass->getMethods();
        foreach ($reflectionMethods as $reflectionMethod) {
            if ($reflectionMethod->name === '__construct') {
                return $reflectionMethod;
            }
        }
        return null;
    }

     /**
     * Get ( or make) one param for _constructor of class which $reflectionClass was taken
     * 
     * If it is Service, ($serviceName isn't null) inforvation from service cofig
     * will be used. Else classes which are specified in {@see __construct()} will used.
     * 
     * @param /ReflectionParameter $reflectionParam
     * @param string|null $serviceName
     * @return object  {@see ReflectionClass::newInstanceArgs()}
     */
    protected function _getConstructParamValue( $reflectionParam, $serviceName = null)    
    {
        $constructParamService = $this
            ->_resolveServiceForConstructParam($reflectionParam, $serviceName);
        if (isset($constructParamService)) {
           $instance = $this->createService($constructParamService);
        }else{                    
            $constructParamClassObject = $reflectionParam->getClass();
            if (!isset($constructParamClassObject)) {
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception(
                    "Cann't resolve class for param  - " . $reflectionParam->getName());
            }
            $constructParamClass = $constructParamClassObject->getName();
            $instance = $this->createObject($constructParamClass);
        }
        return $instance;
    }   

    /**
     * If $option present in params ( first position ) method return array from Service config
     * 
     * @param array $reflectionParams -  array of /ReflectionParameter
     * @param $serviceName string
     * @return array|null  {@see ReflectionClass::newInstanceArgs()}
     */
    protected function _resolveOptions( $reflectionParams, $serviceName= null)
    {        
        //extract option (if exist) from array $reflectionParams
        $isOptionsInConstructParams = isset($reflectionParams[0]) 
            && $reflectionParams[0]->getName() === 'options';    
        if ($isOptionsInConstructParams) {
            if (isset($serviceName)) {
                $optionsFromConfig = $this->_servicesConfigs->getServiceOptions($serviceName);
                $options = $optionsFromConfig;
            }else{
                $options = array();
            }
        }else{
            $options = null;
        }           
        return $options;   
    }    
    
    /**
     * If param is specified as Service in Service config - it will be taken
     * 
     * @param \ReflectionParameter $reflectionParam
     * @param string|null $serviceName 
     * @return object|null
     */
    protected function _resolveServiceForConstructParam( \ReflectionParameter $reflectionParam, $serviceName)
    {   
        if (isset($serviceName)) {
            //Information about params from config for services is more
            //important then types was specified in __construct()
            $constructParamName = $reflectionParam->getName();
            $serviceForConstructParam = $this->_servicesConfigs
               ->getServiceNameForConstructParam($serviceName, $constructParamName);
        }else{
            $serviceForConstructParam = null;            
        } 
        return $serviceForConstructParam;

    }       

    /**
     * One of method of making Service is cloning. 
     * 
     * Reference clone is needs for it. It is stored in {@link $_servicesStore}
     * 
     * @see Zaboy_Services_Interface::CONFIG_VALUE_SINGLETON
     * @see Zaboy_Services_Interface::CONFIG_VALUE_CLONE
     * @see Zaboy_Services_Interface::CONFIG_VALUE_RECREATE
     * @param string $serviceName
     * @return object
     */
    public function getServiceClone($serviceName) 
    {
        // Is etalon Service for clone already was created?
        $etalonServiceInstance = $this->_servicesStore->getEtalon($serviceName);
        if (!isset($etalonServiceInstance)) {
            $etalonServiceInstance = $this->createService($serviceName) ;
            $this->_servicesStore->addEtalon($serviceName, $etalonServiceInstance);
        }
        $serviceInstance = clone $etalonServiceInstance;
        $this->_servicesStore->addService($serviceName, $serviceInstance);
        return $serviceInstance;
    }    
    
}