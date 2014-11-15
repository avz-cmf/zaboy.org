<?php
/**
 * Zaboy_Services_Interface
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Zaboy_Services_Interface
 * 
 * @see Avz_DataStore_Interface
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
interface Zaboy_Services_Interface 
{
    
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
    
    /**
     * 
     * @return array 
     */
    static function getDefaultServiceConfig();
 
}