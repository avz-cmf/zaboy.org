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
     * array of services which are loaded via DIC
     */
    private $_runningServices = array();
    

    /**
     * @return bool
     */
    public function hasService($serviceName)
    { 
        return isset($this->_runningServices[$serviceName]);
    }    
    
    /**
      * @param string
      * @return void
      */    
    public function addService($serviceName, $serviceObject)
    {
        if ($this->hasService($serviceName)) {
             require_once 'Zaboy/Dic/Exception.php';
             throw new Zaboy_Dic_Exception("There is Service with name: $serviceName. You cann't revrite it");    
        }
        $this->_runningServices[$serviceName] = $serviceObject;
    }

    /**
     * @return array
     */
    public function getServices()
    { 
        return $this->_runningServices;
    }

    /**
     * @return object
     */
    public function getService($serviceName)
    {
        if (isset($this->_runningServices[$serviceName])) {
            return $this->_runningServices[$serviceName];
        }else{
            return null;
        }
    }
}