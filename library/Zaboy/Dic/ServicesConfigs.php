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
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_ServicesConfigs extends Zaboy_Abstract
{
    /**
     * for $this->_getBootstrap()->getResource('dic')
     * comfig.ini :  dic.services...
     */
    const DIC_NAME_RESOURCE = 'dic';
    
    /**
     * comfig.ini :  dic.services.serviceName1.class = className1 ... 
     */
    const CONFIG_KEY_CLASS = 'class';
   
    /**
     * comfig.ini :  dic.services.serviceName1.instance = singleton
     */
    const CONFIG_KEY_INSTANCE = 'instance';
    
    /**
     * default value for dic.services.serviceName1.instance
     * comfig.ini :  dic.services.serviceName1.instance = singleton
     */
    const CONFIG_VALUE_SINGLETON = 'singleton';
    
    /**
     * For all request, service will create by clone from etalon
     * comfig.ini :  dic.services.serviceName1.instance = clone
     */
    const CONFIG_VALUE_CLONE = 'clone';
    
    /**
     * For all request, service will create by constuct() call
     * comfig.ini :  dic.services.serviceName1.instance = recreate
     */
    const CONFIG_VALUE_RECREATE = 'recreate';
    
    /**
     * comfig.ini :  dic.services.serviceName1.options.key = val  ...
     */
    const CONFIG_KEY_OPTIONS = 'options'; 
    
    /**
     * dic.services.serviceName1.params.firstparam = serviceName
     */   
    const CONFIG_KEY_PARAMS = 'params';
   
    /**
     * comfig.ini :  dic.services.serviceName1.autoload = true
     * Default value is FALSE.
     */
    const CONFIG_KEY_AUTOLOAD = 'autoload';



    /*
     * Array with configurations of Services
     * 
     * Contains information about Services from application.ini 
     */
    private $_servicesConfigs = array();

     /**
      * Set $this->_servicesConfigs = $options; 
      * 
      * @param array  It's contaned array('serviceName' = array('class' = 'ServiceClass', 'options' ...
      * @return Zaboy_Dic_ServicesConfigs
      */
    public function setOptions(array $options)
    {
        if (isset($options)) {
            $this->_servicesConfigs = $options;           
        }
        return $this;
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
     * There are some variety of making Service
     * 
     * @see Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_SINGLETON
     * @see Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_CLONE
     * @see Zaboy_Dic_ServicesConfigs::CONFIG_VALUE_RECREATE
     * @param string
     * @return string
     */    
    public function getServiceInitiationType($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][self::CONFIG_KEY_INSTANCE])) {
            return $this->_servicesConfigs[$serviceName][self::CONFIG_KEY_INSTANCE];
        }else{
            return self::CONFIG_VALUE_SINGLETON;
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
        if (isset($this->_servicesConfigs[$serviceName][self::CONFIG_KEY_PARAMS][$paramName])) {
            return $this->_servicesConfigs[$serviceName][self::CONFIG_KEY_PARAMS][$paramName];
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
        if (isset($this->_servicesConfigs[$serviceName][self::CONFIG_KEY_AUTOLOAD])) {
            return $this->_servicesConfigs[$serviceName][self::CONFIG_KEY_AUTOLOAD];
        }else{
            return false; //by default
        }
    }
}