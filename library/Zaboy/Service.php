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
* 
 * 
 * The <b>$options</b> ( if it exist as parametr for __construct ) have to first parameter. 
 * More about <i>__consruct()</i> - see {@see Zaboy_Services_Abstract::__consruct()}.<br>
 * More about <i>$options</i> - see {@see Zaboy_Abstract::setOptions()}.<br>
 * You can note options for <i>Service</i>  in config.ini <br>
 * <code> 
 * resources.dic.services.NameOfService.options.param1 = value1
 * resources.dic.services.NameOfService.options.param2 = value2
 * </code>
 * or just in SeviceObjectClass - see $_defaultOptions in {@see Zaboy_Services_Abstract}.<br>
 * 
 * <b>All other parametrs must be Services!</b>. That parametr we call <i>Service Param.</i> <br>
 * <i>Service Param</i> in declaration of __construct must has type.<br>
 * <code>
 *     public function __construct($options, Test_Service_First $testFirst, Test_Service_Next $testNext) 
 *   { ...}
 * </code>
 * <br>
 * You can override type for <i>Service</i> in config.ini <br>
 * For example:<br>
 * <code> 
 * resources.dic.services.NameOfService.class = New_Class_For_NameOfService
 * </code>
 * 
 * <b>About optional parametrs</b><br>
 * Optional  parametrs are transmitted to _consruct if they already was loaded or if they is described in config.ini
 * 
 * <b>Aliases</b><br>
 * You can take 2 or more instaces of <i>Service</i>. Use aliases.
 * <code>
 *     public function __construct(Service_One_Type $firstExemplar, Service_One_Type $secondExemplar) 
 *   { ...}
 * </code>
 * <br>
 * You can load <i>Service</i> () see {@see Zaboy_Dic::get()}:
 * <code>
 *    $dic->get('NameOrAlias'); //if class isn't noted in cofig.ini - exception
 *    //or
 *    $dic->get('NameOrAlias', 'Service_Class'); //if class is noted in cofig.ini, parameter  'Service_Class' ignore.   
 * </code>
 * <i>Service</i> which was loaded is containing in  <i>Dic</i> and can't is load again. <br>
 * 
 * <b>Autoload</b><br>
 * Class will be load if resources.dic.services.WithAutoload.autoload = true, where 'WithAutoload' is services name <br>
 * 
 * <br><b>What is NameOfService?</b><br>
 * It is string - param for (@see Zaboy_Dic::get()} and (@see Zaboy_Dic::has()}<br>
 * See more about it : (@see Zaboy_Dic_Interface)
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
    
}