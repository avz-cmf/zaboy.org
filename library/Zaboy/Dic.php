<?php
/**
 * Zaboy_Dic
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  require_once 'Zaboy/Dic/ServicesConfigs.php';
  require_once 'Zaboy/Dic/LoopChecker.php';
  require_once 'Zaboy/Dic/ServicesStore.php';
  
/**
 * <b>Zaboy_Dic</b><br>
 * 
 * Zaboy_Dic is dependency injection container class (<tt>Dic</tt>). <tt>Dic</tt> 
 * load as resurce plugin of Bootstrap {@see Zaboy_Application_Resource_Dic}. 
 * 
 * Add in <tt>application.ini</tt> for load {@see Zaboy_Dic}<br>
 * <pre> 
 * resources.dic[] = 
 * </pre>
 * You can change class of {@see Zaboy_Dic} for load:
 * <pre> 
 * resources.dic.class = My_Dic_Class 
 * </pre>
 * For more information about configuration see {@see Zaboy_Dic_ServicesConfigs} 
 * and {@see Zaboy_Application_Resource_Dic}<br>  
 * You can get <tt>Dic</tt>  as resource: <br>
 * <code> 
 *   $dic = $bootstrap->getResource('dic');
 * </code>
 * <br>
 * 
 * <b>Objects and Services for DIC</b><br>
 * Zaboy_Dic can load any object which corresponds a some requirements.<br>
 * Zaboy_Dic also can contane that object if it is described in Services Config 
 * ( in most cases it is application.ini).
 * In that case this object is Service. <br>
 * Ordinary Objects ( not Services ), which was loaded via Dic is not stored in Dic. 
 * Every call {@See get()} will return new object for them. <br>
 * For Services you will get from {@See Zaboy_Dic::get()} singleton instance by default. 
 * 
 * <b>Requirements for Objects, which will load via DIC</b><br>
 * There are some requirements for <tt>__consruct()</tt> of Objects, which will load by <tt>Dic</tt>.
 * Next variants is possibly:
 * <code> 
 * //constructor is allowed not to have parameters
 * function __construct()
 * 
 * //there is $options only. Param has to have name $options
 * function __construct(array $options)
 * 
 * //There are some object type parameters with specified class name  
 * function __construct(Class_Name $object1, Another_Class $object2, ...)
 * //or 
 * function __construct(array $options, Class_Name $object1,  Another_Class ...)
 * //the $options if it exist as parametr for __construct have to be a first parameter.
 * 
 * //There are some parameters which is Services with specified class name or not
 * function __construct(Service_Class $service1, Another_Service_Class $service2, ...)
 * function __construct(array $options, Service_Class $service1, Another_Service_Class ...)
 * 
 * //According to requirements for Service describe, class of Service have to 
 * //specified in Service Config. Because this reason, you may have not to notify classes
 * // for Services in parameters, but it is recommended for type control. 
 * function __construct(Service_Class $service1, $service2  ...)
 * function __construct(array $options, $service1, $service2  ...)
 * 
 * //And all together:
 * function __construct(array $options, Service_Class $service1, $service2, Class_Name $object1)
 * //In this case we must have in Services config:
 * </code>
 * <pre> 
 * resources.dic.service1.class = Service_Class
 * resources.dic.service2.class = Next_Service_Class
 * </pre>
 * 
 * In another words: only objects in parameters except array of options which 
 * has to be in first place or can be absent.<br>
 * 
 * About optional parametrs in <tt>__consruct():</tt><br>
 * All optional params will be ignored 
 * (NULL will be retrived instead of them in to consructor).<br>
 * NOTED: you can use this parameters in runtime - see {@see Zaboy_Dic::getOptionalParamValue()}
 * 
 * <b>Loading ordinary Objects (not Services) via DIC</b><br>
 * <code>
 * $dic->get(null, Class_Name);
 * // or
 * $dic->get('anyName', Class_Name);
 * //if 'anyName' isn't described in <tt>application.ini</tt> as Service - it will be ignored
 * </code>
 * Objects is not stored in Dic. 
 * Every call {@See Zaboy_Dic::get()} will return new object<br>
 * All dependencies will be loaded recursively.
 * 
 * <b>Loading Services via DIC</b><br>
 * <code>
 * $dic->get('ServiceName');
 * // or
 * $dic->get('ServiceName', Class_Name);
 * //'Class_Name' will be ignored
 * </code>
 * Objects is not stored in Dic. 
 * Every call {@See Zaboy_Dic::get()} will return singleton by default.
 * You can change it in <tt>application.ini</tt><br>
 * See {@see Zaboy_Dic_ServicesConfigs, Zaboy_Services} for details.<br>
 * All dependencies will be loaded recursively.
 * 
 * <b>What different betwin Object and Service</b><br>
 * Only one - Service is described in <tt>application.ini</tt><br>
 * 
 * For more information about Objects, Services and requirements for them, 
 * see {@see Zaboy_Services}<br>
 * 
 * <b>Dependencies in __construct</b><br>
 * When you load Object or Service via $dic->get(), all dependencies will be loaded
 * recursively. Optional params will be ignored. But you can load and use these  
 * dependencies later. See {@see getOptionalParamValue()} and 
 * magic methods in {@see Zaboy_Services}.
 * 
 * @todo Optional Params  - make method getLazyLoadedService($ServiceName)
 * @see Zaboy_Dic_Interface
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic extends Zaboy_Abstract
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
     * @var Zaboy_Dic_Factory
     */
    private $_factory;
        
    /**
     * 
     * @param array $options
     * @param Zaboy_Dic_ServicesConfigs $servicesConfigs
     * @param Zaboy_Dic_ServicesStore $servicesStore
     * @param Zaboy_Dic_Factory $factory
     * @return void
     */
    public function __construct( 
        array $options, 
        Zaboy_Dic_ServicesConfigs $servicesConfigs,
        Zaboy_Dic_ServicesStore $servicesStore,
        Zaboy_Dic_Factory $factory        
    ){
        parent::__construct($options);     
        $this->_servicesConfigs = $servicesConfigs;
        $this->_servicesStore = $servicesStore;        
        $this->_factory = $factory;
        
        $this->_autoloadServices();
    }  
    
    /**
     * Return Service if is described or just object with injected dependencies
     * 
     * @param string $name
     * @param string|null $class
     * @return object|null
     */
    public function get($name, $class = null)    
    {
        if ($this->_servicesConfigs->isService($name)) {
            $instance = $this->_getService($name); 
        }else{
            // $name is not Service name     
            if (!isset($class)) {
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception(
                    'can not resolve class for - ' . $name
                ); 
            }                
            $instance = $this->_factory->createObject($class);            
        }
        return $instance;
    }

    /**
     * Return Service Name by Service Object
     * 
     * @param object $serviceInstance usually $this
     * @return string|null Service Name
     */
    public function getRunningServiceName($serviceInstance) 
    {
        return $this->_servicesStore->getRunningServiceName($serviceInstance);
    }
     
    /**
     * Return Service Instance(s) by Name of Service. Method return ARRAY!
     * 
     * If Service Name isn't specified all running services will be returned
     * Format of returnet data (if $serviceName = null):
     * <code>
     * [
     *  singletonServiceName1 = [ 0 => instanceSingletonService1]
     *  singletonServiceName2 = [ 0 => instanceSingletonService2]
     *  clonedServiceName1 = 
     *      [   
     *          0 => instanceClonedService11
     *          1 => instanceClonedService12
     *          ...
     *      ]
     * ]
     * </code>
     * if $serviceName = singletonServiceName1 :
     * <code>
     * [
     *  singletonServiceName1 = [ 0 => instanceSingletonService1]
     * </code>
     * 
     * @param  string $serviceName  Service Name
     * @return array|null Instance(s)
     */
    public function getRunningServiceInstance($serviceName = null) 
    {
        $services = $this->_servicesStore->getRunningServices();
        if (is_null($serviceName)) {
            return $services;
        }else{
            if (isset($services[$serviceName])) {
                return $services[$serviceName];
            }else{
                return null;
            }
        }
    }   
    
    /**
     * Uses for lazy load Services and object for optional parameters of __constructor
     * 
     * See magic methods in {@see Zaboy_Services} for exavple of uses.
     * 
     * @param object $instance it is $this in most cases
     * @param string $paramName name of optional parameter of __constructor
     * @return object
     */
    public function getOptionalParamValue($instance, $paramName) 
    {
        if ($paramName === 'options') {
            require_once 'Zaboy/Dic/Exception.php';
            throw new Zaboy_Dic_Exception(
                'You cann\'t lazy load $options'
            ); 
        }
        $serviceName = $this->getRunningServiceName($instance);
        $className = get_class($instance);
        $reflectionClass = new ReflectionClass($className);  
        $lazyLoadedParam = $this
            ->_factory
                ->getOptionalParamValue(
                    $paramName, 
                    $reflectionClass, 
                    $serviceName
                )
        ;
        return $lazyLoadedParam; 
    }

    /**
     * Some services have to be started automatically
     * 
     * Class must be load if in config
     * <code>
     * resources.dic.services.WithAutoload.autoload = true
     * </code>
     * where 'WithAutoload' is services name
     * 
     * @return void
     */
    protected function _autoloadServices() {
        $servicesNames = $this->_servicesConfigs->getServicesNames();
        foreach ($servicesNames as $serviceName ) {
            if (  $this->_servicesConfigs->getServiceAutoload($serviceName)) {
                $this->get($serviceName);
            }
        }
     }    

    /**
     * Instantiate service
     * 
     * @param type $serviceName
     * @return null
     */
    protected function _getService($serviceName)    
    {
        if (!$this->_servicesConfigs->isService($serviceName)) {
            return null;
        }   
        $initiationType = $this->_servicesConfigs->getServiceInitiationType($serviceName);
        switch ($initiationType) {
            case Zaboy_Services_Interface::CONFIG_VALUE_SINGLETON :
                // Is Service with $serviceName already was loaded?
                $serviceInstance = $this->_servicesStore->getRunningSingletonService($serviceName);
                if (!isset($serviceInstance)) {
                    $serviceInstance = $this->_factory->createService($serviceName);
                    $this->_servicesStore->addService($serviceName, $serviceInstance);
                }
                break;
            case Zaboy_Services_Interface::CONFIG_VALUE_RECREATE :
                $serviceInstance = $this->_factory->createService($serviceName);
                $this->_servicesStore->addService($serviceName, $serviceInstance);
                break;
            case Zaboy_Services_Interface::CONFIG_VALUE_CLONE :
                $serviceInstance = $this->_factory->getServiceClone($serviceName);
                break;
            default:
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception(
                    'Unknow initiation type - ' . $initiationType
                ); 
        }
        return $serviceInstance;
    }   
         
}