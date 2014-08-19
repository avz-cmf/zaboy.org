<?php
/**
 * Zaboy_Service
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  
/**
 * Zaboy_Service
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Service extends Zaboy_Abstract
{
    /**
     * Key for {@see Zaboy_Abstract::_attribs}
     * 
     * @see Zaboy_Abstract::setOptions()
     */
         const OPTIONS_KEY_INJECTED_SERVICES = 'injectedServices';    
    
    /**
     * If $options haven't submitted via __construct() - it is used
     * 
     * @var array default Options
     */   
     protected $_defaultOptions = array();

    /**
    * Service constructor 
     * 
     * Constructor's params of Service must be (array $options, Servece_Class $servece1, Another_Servece_Class $servece2, ...)
     * or (Servece_Class $servece1, Another_Servece_Class $servece2, ...)) or just ()
     * 
     * @param array
     * @return void
     */  
    public function __construct() 
    {
        $argsArray = func_get_args();
         if (empty($argsArray[0])){
            $options = $this->_defaultOptions;     
         }else{
             // if first param is array - we consider what that is options
            if (is_array($argsArray[0])) {
                $options = array_shift($argsArray);
            }            
         }    
         $options[self::OPTIONS_KEY_INJECTED_SERVICES] = array();
        // Services which was gotten via __construct()  will be contained 
        // in $options as array with Key "injectedServices"
        foreach ($argsArray as $serviceObject) {
            $serviceName = $this->_getDic()->getServiceName($serviceObject);
            $options[self::OPTIONS_KEY_INJECTED_SERVICES][$serviceName] = $serviceObject;
        }
        // See Zaboy_Abstract::setOptions(array $options) and  See Zaboy_Abstract::setAttrib($key, $value)
        parent::__construct($options);
    }
    
    /**
      * 
      * @return aray (name1, name2 ..) or array() if ampty
      */
     public function getInjectedServicesNames() 
     {
        $injectedServices = $this->getAttrib(self::OPTIONS_KEY_INJECTED_SERVICES);
        return array_keys ($injectedServices); 
     }
    
    /**
      * @param string name of service
      * @return Zaboy_Service
      */
     public function getInjectedService($serviceName) 
     {
        $injectedServicesArray = $this->getAttrib(self::OPTIONS_KEY_INJECTED_SERVICES);
        if (isset($injectedServicesArray[$serviceName])) {
            return $injectedServicesArray[$serviceName];
        }else{
            return null;    
        }

     }
     
    /**
     * @return Zend_Application_Bootstrap_Bootstrap
     */
    protected function _getBootstrap()
    {
        global $application;
        /* @var $application Zend_Application */
        $bootstrap = $application->getBootstrap();
        return $bootstrap;
    }     

      /**
      * @return Zaboy_Dic
      */
     protected function  _getDic() 
     {
        $bootstrap = $this->_getBootstrap();
        $dic = $bootstrap->getResource('dic');     
        return $dic;
     }       
}