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
 * <b>Services Config/<b><br>
 * Usialy Services Config is part of DIC config in application.ini with key 
 * which is defined in {@see Zaboy_Dic::CONFIG_KEY_SERVICE}<br> 
 * DIC have got config form resurce plugin { @see Zaboy_Application_Resource_Dic}<br>
 * Part of application.ini:<br>
 *<pre>
 * dic.service.serviceName.class = ServiceClass
 * dic.service.serviceName.options.key11 = val11
 * dic.service.serviceName.options.key12 = val12
 * dic.service.serviceName.autoload = true
 * dic.service.nextServiceName.class = NextServiceClass 
 * ...
 *</pre>
 * It is in $servicesConfig (see{@see setConfigsServices()}
 *<code>
 * array(
 *     'serviceName' = array(
 *         'class' = 'ServiceClass'
 *         'options' = array(
 *             'key1' = val1
 *             'key2' = val2
 *         (
 *         'autoload' = true
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
    const DIC_NAME_RESOURCE = 'dic'; // for $this->_getBootstrap()->getResource('dic')
    
     const CONFIG_KEY_CLASS = 'class';       //  comfig.ini :  dic.service.serviceName1.class = className1 ... 
     const CONFIG_KEY_OPTIONS = 'options';   //  comfig.ini :  dic.service.serviceName1.options.key = val  ...
     const CONFIG_KEY_AUTOLOAD = 'autoload'; //  comfig.ini :  dic.service.serviceName1.autoload = true

     const CONFIG_KEY_INSTANCE = 'instance';    //  comfig.ini :  dic.service.serviceName1.instance = singleton
     const CONFIG_VALUE_SINGLETON = 'singleton'; //  default value for dic.service.serviceName1.instance
     const CONFIG_VALUE_CLONE = 'clone';        //  For all request, service will create by clone from etalon   
     const CONFIG_VALUE_RECREATE = 'recreate';     //  For all request, service will create by constuct() call

    const CONFIG_KEY_PARAMS = 'params';    // dic.service.serviceName1.params.firstparam = serviceName
     
    /*
     * array configurations of Services
     * 
     * Contains information about Services from application.ini and
     * from constuctors calling Services 
     */
    private $_servicesConfigs = array();

     /**
      * Call setters for elements $options if exist and rest copy to {@see Zaboy_Abstract::_attribs}
      * 
      * May be two cases for property $options['oneProperty'] = value
      * If method setOneProperty is exist - it will be call, else $this->_attribs['oneProperty'] = value
      *
      * @param array  array('serviceName' = array('class' = 'ServiceClass', 'options' ...
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
      * @param string
      * @return string
      */    
    public function getServiceInitiation($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][self::CONFIG_KEY_INSTANCE])) {
            return $this->_servicesConfigs[$serviceName][self::CONFIG_KEY_INSTANCE];
        }else{
            return self::CONFIG_VALUE_SINGLETON;
        }
    }    
        
    /**
     *Return TRUE if service with same name was described in config
     * 
     * @param string
     * @return bool
     */    
    public function isService($serviceName) 
    {
        $servicesNamesArray = $this->getServicesNames();
        return in_array($serviceName, $servicesNamesArray);
    }        
    
    
    public function getServiceForConstructParam($serviceName, $paramName)
    {  
        if (isset($this->_servicesConfigs[$serviceName][self::CONFIG_KEY_PARAMS][$paramName])) {
            return $this->_servicesConfigs[$serviceName][self::CONFIG_KEY_PARAMS][$paramName];
        }else{
            return null;
        }
    }
    
    /**
      * @param string
      * @return bool
      */    
    public function _getServiceAutoload($serviceName)
    {
        if (isset($this->_servicesConfigs[$serviceName][self::CONFIG_KEY_AUTOLOAD])) {
            return $this->_servicesConfigs[$serviceName][self::CONFIG_KEY_AUTOLOAD];
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
            if ( (bool) $this->_getServiceAutoload($serviceName)) {
                $this->_getBootstrap()
                        ->getResource(self::DIC_NAME_RESOURCE)
                        ->get($serviceName);
            }
        }
     }    
}