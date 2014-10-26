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
        if (count($argsArray)===0) {
            parent::__construct($this->_defaultOptions);
            return;
        }    
        $paramNumber = 0;
        if (is_array($argsArray[0])) {
            if ($argsArray[0] === array()) {
                $options = $this->_defaultOptions;
            }else{
                $options = $argsArray[0];
            }
            array_shift($argsArray);
            $paramNumber = $paramNumber + 1; 
        }    
        $options[self::OPTIONS_KEY_INJECTED_SERVICES] = array();
       // Services which was gotten via __construct()  will be contained 
       // in $options as array with Key "injectedServices"
        foreach ($argsArray as $serviceObject) {
            $paramNumber = $paramNumber + 1; 
            $serviceName = $this->_getDic()->getServiceName($serviceObject);
            if (!isset($serviceName)) {
                $serviceName = 'Parameter_' . $paramNumber;
            }
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
        if (isset($injectedServices)) {
            return array_keys ($injectedServices);             
        }else{
            return array();
        }

     }
     
    public function getLazyLoadService($constructParamName)    
    {
        
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
      * @return Zaboy_Dic
      */
     protected function  _getDic() 
     {
        $bootstrap = $this->_getBootstrap();
        $dic = $bootstrap->getResource('dic');     
        return $dic;
     }       
}