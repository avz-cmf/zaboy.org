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
            $instance = $this->getService($name); 
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
     * Instantiate service
     * 
     * @param type $serviceName
     * @return null
     */
    public function getService($serviceName)    
    {
        if (!$this->_servicesConfigs->isService($serviceName)) {
            return null;
        }   
        
        $initiationType = $this->_servicesConfigs->getServiceInitiationType($serviceName);
        switch ($initiationType) {
            case Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_SINGLETON :
                // Is Service with $serviceName already was loaded?
                $serviceInstance = $this->_servicesStore->getRunningSingletonService($serviceName);
                if (!isset($serviceInstance)) {
                    $serviceInstance = $this->_factory->createService($serviceName);
                    $this->_servicesStore->addService($serviceName, $serviceInstance);
                }
                break;
            case Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_RECREATE :
                $serviceInstance = $this->_factory->createService($serviceName);
                $this->_servicesStore->addService($serviceName, $serviceInstance);
                break;
            case Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_CLONE :
                $serviceInstance = $this->_factory->_getServiceClone($serviceName);
                break;
            default:
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception(
                    'unknow initiation type - ' . $initiationType
                ); 
        }
        
        return $serviceInstance;
    }   
    
    /**
     * Return Service Name by Service Object
     * 
     * @param object $serviceInstance usually $this
     * @return string|null Service Name
     */
    public function getRunningServiceName($serviceInstance) 
    {
        $this->_servicesStore->getRunningServiceName($serviceInstance);
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
    private function _autoloadServices() {
        $servicesNames = $this->_servicesConfigs->getServicesNames();
        foreach ($servicesNames as $serviceName ) {
            if (  $this->_servicesConfigs->getServiceAutoload($serviceName)) {
                $this->get($serviceName);
            }
        }
     }    
}