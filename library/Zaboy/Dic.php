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
        
        $initiationType = $this->_servicesConfigs->getServiceInitiationType($serviceName);
        switch ($initiationType) {
            case Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_SINGLETON :
                // Is Service with $serviceName already was loaded?
                $serviceInstance = $this->_servicesStore->getSingletonService($serviceName);
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
                $serviceInstance = $this->_getServiceClone($serviceName);
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

    private function _getServiceClone($serviceName) 
    {
        // Is etalon Service for clone already was created?
        $etalonServiceInstance = $this->_servicesStore->getEtalon($serviceName);
        if (!isset($etalonServiceInstance)) {
            $etalonServiceInstance = $this->_factory->createService($serviceName) ;
            $this->_servicesStore->addEtalon($serviceName, $etalonServiceInstance);
        }
        $serviceInstance = clone $etalonServiceInstance;
        $this->_servicesStore->addService($serviceName, $serviceInstance);
        return $serviceInstance;
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

}