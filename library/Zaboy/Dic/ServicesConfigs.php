<?php
/**
 * Zaboy_Dic_ServicesConfigs
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Services/Interface.php';
  
/**
 * Zaboy_Dic_ServicesConfigs
 * 
 * <b>Services Config</b><br>
 * Usialy Services Config is part of DIC config in application.ini with key 
 * which is defined in {@see Zaboy_Application_Resource_Dic::CONFIG_KEY_SERVICE}<br> 
 * DIC have got config form resurce plugin {@see Zaboy_Application_Resource_Dic}<br>
 * This is part of application.ini:<br>
 *<pre>
 * dic.services.serviceName.class = ServiceClass
 * dic.services.serviceName.options.key11 = val11
 * dic.services.serviceName.options.key12 = val12
 * dic.services.serviceName.autoload = true
 * dic.services.serviceName.params.secondParam = OtherServiceName 
 * dic.services.nextServiceName.class = NextServiceClass 
 * ...
 *</pre>
 * It is in $servicesConfig (see{@see setConfigsServices()})
 *<code>
 * array(
 *     'serviceName' = array(
 *         'class' = 'ServiceClass'
 *         'options' = array(
 *             'key1' = val1
 *             'key2' = val2
 *         (
 *         'autoload' = true
 *         'params' = array(
 *             'secondParam' = 'OtherServiceName' 
 *         ) 
 *     )
 * )
 * array(
 *     'nextServiceName' = array(
 *         'class' = 'NextServiceClass'
 *          ...
 *</code>
 * 
 * There are next keys for using after <tt>'dic.services.'</tt> in appalication .ini:<br>
 * <ul>
 *     <li><tt>class</tt> See:{@see Zaboy_Services::CONFIG_KEY_CLASS} </li>
 *     <li>
 *         <tt>instance</tt> See:{@see Zaboy_Services::CONFIG_KEY_INSTANCE}
 *         <ul>
 *             <li><tt>singleton</tt> See:{@see Zaboy_Services::CONFIG_VALUE_SINGLETON} </li>
 *             <li><tt>clone</tt> See:{@see Zaboy_Services::CONFIG_VALUE_CLONE} </li> 
 *             <li><tt>recreate</tt> See:{@see Zaboy_Services::CONFIG_VALUE_RECREATE} </li> 
 *         </ul> 
 *     </li>
 *     <li><tt>options</tt> See:{@see Zaboy_Services::CONFIG_KEY_OPTIONS} </li>
 *     <li><tt>params</tt> See:{@see Zaboy_Services::CONFIG_KEY_PARAMS} </li>
 * </ul> 
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_ServicesConfigs
{

    /*
     * Array with configurations of Services
     * 
     * Contains information about Services from application.ini 
     */
    private $_servicesConfigs = array();
    
    /**
     * I houpe you know what is __construct()
     * 
     * @param array  $servicesConfigs It's contaned array('serviceName' = array('class' = 'ServiceClass', 'options' ...
     */
    public function __construct($servicesConfigs) {
        $this->_setServicesConfigs($servicesConfigs);
    }

    /**
      * Set $this->_servicesConfigs = $options; 
      * 
      * @param array  It's contaned array('serviceName' = array('class' = 'ServiceClass', 'options' ...
      * @return Zaboy_Dic_ServicesConfigs
      */
     private function _setServicesConfigs(array $servicesConfigs)
    {
        foreach ($servicesConfigs as $serviceName => $serviceConfig) {
            if (isset($servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_CLASS])) {
                $serviceClass = $servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_CLASS];
            }else{
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception(
                    "There isn't 'class' key in service config for service: $serviceName");
            }
            if (is_a ( $serviceClass, 'Zaboy_Services_Interface' , true)){
                $defaultConfig = $serviceClass::getDefaultServiceConfig();
                if (isset($defaultConfig[Zaboy_Services_Interface::CONFIG_KEY_CLASS])) {
                    require_once 'Zaboy/Dic/Exception.php';
                    throw new Zaboy_Dic_Exception(
                        "You try set default class in $serviceClass for service $serviceName");
                }
                $serviceConfig = array_merge($defaultConfig, $serviceConfig);
            }
            $this->_servicesConfigs[$serviceName] = $serviceConfig; 
        }
    }     
    
    /**
     * return array ( 0=>ServaceName, 1=> NextServiceName ...)
     * 
     * @return array ( 0=>ServaceName, 1=> NextServiceName ...)
     */    
    public function getServicesNames()
    {
        return array_keys (  $this->_servicesConfigs );
    }
    
    /**
     * Return Service Class Name or null if absent
     * 
     * @param string
     * @return string|null
     */    
    public function getServiceClass($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_CLASS])) {
            return $this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_CLASS];
        }else{
            return null;
        }
    }
    
    /**
     * About Service options see {@see Zaboy_Services_Interface::CONFIG_KEY_OPTIONS}
     * 
     * @param string
     * @return array
     */    
    public function getServiceOptions($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_OPTIONS])) {
            return $this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_OPTIONS];
        }else{
            return array();
        }
    }
    
    /**
     * There are some variety of making Service
     * 
     * @see Zaboy_Services_Interface::CONFIG_VALUE_SINGLETON
     * @see Zaboy_Services_Interface::CONFIG_VALUE_CLONE
     * @see Zaboy_Services_Interface::CONFIG_VALUE_RECREATE
     * @param string
     * @return string
     */    
    public function getServiceInitiationType($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_INSTANCE])) {
            return $this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_INSTANCE];
        }else{
            return Zaboy_Services_Interface::CONFIG_VALUE_SINGLETON;
        }
    }    
        
    /**
     * Return TRUE if service with same name was described in config
     * 
     * @param string
     * @return bool
     */    
    public function isService($serviceName) 
    {
        $servicesNamesArray = $this->getServicesNames();
        return in_array($serviceName, $servicesNamesArray);
    }        
    
    /**
     * 
     * @param string $serviceName
     * @param string $paramName
     * @return string|null
     */
    public function getServiceNameForConstructParam($serviceName, $paramName)
    {  
        if (isset($this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_PARAMS][$paramName])) {
            return $this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_PARAMS][$paramName];
        }else{
            return null;
        }
    }
    
    /**
     * Return TRUE if service has to be started automatically
     * 
     * By default is FALSE
     * 
     * @param string
     * @return bool
     */    
    public function getServiceAutoload($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_AUTOLOAD])) {
            return $this->_servicesConfigs[$serviceName][Zaboy_Services_Interface::CONFIG_KEY_AUTOLOAD];
        }else{
            return false; //by default
        }
    }
}