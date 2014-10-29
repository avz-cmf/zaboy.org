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
class Zaboy_Dic_ServicesStore extends Zaboy_Abstract
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
    private $_etalonSevices = array();
    
    /**
    * 
    * @see $_options
    * param array <b>options</b> - options from application.ini. <br>It is all after  <i>resources.dic.</i>
    * @return void
    */  
    public function __construct
    ( 
        array $options, 
        Zaboy_Dic_ServicesConfigs $servicesConfigs
    ){
        parent::__construct($options);     
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
     * @return array
     */
    public function getServices()
    { 
        return $this->_runningServices;
    }

    /**
     * @return object|array
     */
    public function getSingletonService($serviceName)
    {
        if (isset($this->_runningServices[$serviceName])) {
            return $this->_runningServices[$serviceName][0];
        }else{
            return null;
        }
    }
    
    public function addEtalon($serviceName, $serviceObject) 
    {
        $this->_etalonSevices[$serviceName] = $serviceObject;

    }
    
    public function getEtalon($serviceName) {
        if (isset($this->_etalonSevices[$serviceName])) {
            return $this->_etalonSevices[$serviceName];
        }else{
            return null;
        }
    }

    public function getServiceName($serviceInstance)
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