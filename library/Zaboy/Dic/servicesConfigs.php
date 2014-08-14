<?php
/**
 * Zaboy_Dic_ServicesConfigs
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  
/**
 * Zaboy_Dic_ServicesConfigs
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_ServicesConfigs extends Zaboy_Abstract
{
     const CONFIG_KEY_CLASS = 'class';       //  comfig.ini :  dic.service.serviceName1.class = className1 ... 
     const CONFIG_KEY_OPTIONS = 'options';   //  comfig.ini :  dic.service.serviceName1.options.key = val  ...
     const CONFIG_KEY_AUTOLOAD = 'autoload'; //  comfig.ini :  dic.service.serviceName1.autoload = true
    
    
  
    /*
     * Zaboy_Dic
     */
    private $_dic;   
  
    /**
    * param Zaboy_Dic $dic
    * @return void
    */  
    public function __construct( Zaboy_Dic $dic)
    {
        $this->_dic = $dic;
    }        
    
    /*
     * array configurations of Services
     * 
     * Contains information about Services from application.ini and
     * from constuctors calling Services 
     */
    private $_servicesConfigs = array();
    
    /**
      * @param array
      * @return void
      */    
    public function setConfigsServices($servicesConfigs)
    {
        $this->_servicesConfigs = $servicesConfigs;
    }
 
    
    /**
      * @param string
      * @return string|null
      */    
    public function getServiceClass($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][self::CONFIG_KEY_CLASS])) {
            return $this->_servicesConfigs[$serviceName][self::CONFIG_KEY_CLASS];
        }else{
            return null;
        }
    }
    
    /**
      * @param string
      * @return array
      */    
    public function getServiceOptions($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][self::CONFIG_KEY_OPTIONS])) {
            return $this->_servicesConfigs[$serviceName][self::CONFIG_KEY_OPTIONS];
        }else{
            return array();
        }
    }
    
    /**
      * @param string
      * @return bool
      */    
    public function _getServiceAutoload($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][Zaboy_Dic::CONFIG_KEY_AUTOLOAD])) {
            return $this->_servicesConfigs[$serviceName][Zaboy_Dic::CONFIG_KEY_AUTOLOAD];
        }else{
            return false;
        }
    }
    
    /**
     * Class must be load if resources.dic.service.WithAutoload.autoload = true, where 'WithAutoload' is services name
     * 
     * @return void
     */
    public function autoloadServices() {
        foreach ($this->_servicesConfigs as $serviceName => $serviceConfig ) {
            if ( $this->_getServiceAutoload($serviceName)) {
                $this->_dic->get($serviceName);
            }
        }
     }    
}