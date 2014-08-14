<?php
/**
 * Zaboy_Services
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  
/**
 * Zaboy_Services
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Services extends Zaboy_Abstract
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
        if ( !isEmpty($argsArray) ){
            $mayBeOptions = $argsArray[0];
            // if first param is array - we consider what that is options
            if (is_array($mayBeOptions)) {
                $options = array_shift($argsArray);
            }else{
                $options = $this->_defaultOptions; 
            }
            // Services which was gotten via __construct()  will be contained 
            // in $options as array with Key "injectedServices"
            foreach ($argsArray as $serviceObject) {
                $serviceName = $this->_getDic()->getServiceName($serviceObject);
                $options[seff::OPTIONS_KEY_INJECTED_SERVICES][$serviceName] = $serviceObject;
            }    
        }else{
            $options = $this->_defaultOptions; 
        }    
        parent::__construct($options);
    }
    
     /**
      * 
      * @param string name of service
      * @return Zaboy_Service|aray|null
      */
     protected function getInjectedServices($serviceName = null) 
     {
        $injectedServicesArray = $this->getAttrib(seff::OPTIONS_KEY_INJECTED_SERVICES);
        if (!isset($serviceName) && isset($injectedServicesArray)) {
            return  $injectedServicesArray;
        }         
        if (isset($serviceName) && isset($injectedServicesArray[$serviceName])) {        
            return $injectedServicesArray[$serviceName];
        }
        return null;
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