<?php
/**
 * Zaboy_Dic_ServicesStore
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  
/**
 * Zaboy_Dic_ServicesStore
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_ServicesStore
{
    /*
     * @var Zaboy_Dic_ServicesConfigs
     */
    private $_servicesConfigs;    
    
    /*
     * array of services which are loaded via DIC
     */
    private $_runningServices = array();
    
    /*
     * array of services which will be cloned
     */
    private $_etalonsServices = array();
    
    /**
     * 
     * @param Zaboy_Dic_ServicesConfigs $servicesConfigs
     * @return void
     */
    public function __construct
    ( 
        Zaboy_Dic_ServicesConfigs $servicesConfigs
    ){
        $this->_servicesConfigs = $servicesConfigs;
    }     
    
    /**
     * @param string
     * @return void
     */    
    public function addService($serviceName, $serviceObject)
    {
        $initiationType = $this->_servicesConfigs->getServiceInitiationType($serviceName);
        if ($initiationType === Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_SINGLETON
                || $initiationType === Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_RECREATE
                || $initiationType === Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_CLONE 
        ) {
                $this->_runningServices[$serviceName][] = $serviceObject;
        }else{                  
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception(
                    'unknow initiation type - ' . $initiationType
            );
        }        
    }

    /**
     * Return an array with names of running Services
     * 
     * @return array array with names of running Services
     */
    public function getRunningServices()
    { 
        return $this->_runningServices;
    }

    /**
     * Return instance of Service if it was run and has singleton type
     * 
     * @return object|null
     */
    public function getRunningSingletonService($serviceName)
    {
        if ($this->_servicesConfigs->getServiceInitiationType($serviceName)
                !== Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_SINGLETON
        ) {
            require_once 'Zaboy/Dic/Exception.php';
            throw new Zaboy_Dic_Exception(
                "Service $serviceName isn\'t singleton type  - ");
        }
        
        if (isset($this->_runningServices[$serviceName])) {
            return $this->_runningServices[$serviceName][0];
        }else{
            return null;
        }
    }
    
    /**
     * Add etalon Service instance for making Services
     * 
     * One of methods of making Service is cloning. 
     * Reference clone is needs for it. It is stored in {@link $_etalonsServices}
     * 
     * @see Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_CLONE
     * @param string $serviceName
     * @param object $serviceObject
     */
    public function addEtalon($serviceName, $serviceObject) 
    {
        $this->_etalonsServices[$serviceName] = $serviceObject;

    }
    
    /**
     * Get etalon Service instance for making Services
     * 
     * One of methods of making Service is cloning. 
     * Reference clone is needs for it. It is stored in {@link $_etalonsServices}
     * 
     * @see Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_CLONE
     * @param string $serviceName
     * @return object|null
     */
    public function getEtalon($serviceName) {
        if (isset($this->_etalonsServices[$serviceName])) {
            return $this->_etalonsServices[$serviceName];
        }else{
            return null;
        }
    }

    /**
     * Return Service Name by its instance
     * 
     * Some Services can have same names if they was cloned or recreated
     * 
     * @param object $serviceInstance
     * @return string|null
     */
    public function getRunningServiceName($serviceInstance)
    {
        foreach ($this->_runningServices as $serviceName => $runningServices) {
            foreach ($runningServices as $runningService) {
                if($serviceInstance === $runningService){
                    return $serviceName;
                }
            }
        }
        return null;
    }    
}