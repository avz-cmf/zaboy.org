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
     * All strings record which specified Service have to begin with 
     * <pre> dic.services. </pre>
     * If you want to define Service you must specified Service Name and Service Class
     * in <tt>application.ini</tt>:
     * <pre> dic.services.serviceName.class = Service_Class_Name </pre>
     * It is minimal requrement for Service definition.
     */
    const CONFIG_KEY_CLASS = 'class';    
   
    /**
     * You can specified how Service will be loaded by Dic.<br>
     * By default, all calls 
     * <code>
     * $dic->get('serviceName');
     * </code> 
     * return singleton.
     * Example of use in <tt>application.ini</tt>:
     * <pre> dic.services.serviceName1.instance = singleton </pre>
     * See also {@see getDefaultServiceConfig()}
     */
    const CONFIG_KEY_INSTANCE = 'instance'; 
    
    /**
     * One of three possibly values for key {@see Zaboy_Services_Interface::CONFIG_KEY_INSTANCE}<br>
     * Example of use in <tt>application.ini</tt>:
     * <pre> dic.services.serviceName1.instance = singleton </pre>
     * It is default value for Services
     */
    const CONFIG_VALUE_SINGLETON = 'singleton';
    
    /**
     * One of three possibly values for key {@see Zaboy_Services_Interface::CONFIG_KEY_INSTANCE}<br>
     * Example of use in <tt>application.ini</tt>:
     * <pre> dic.services.serviceName1.instance = clone </pre>
     */
    const CONFIG_VALUE_CLONE = 'clone';
    
    /**
     * One of three possibly values for key {@see Zaboy_Services_Interface::CONFIG_KEY_INSTANCE}<br>
     * Example of use in <tt>application.ini</tt>:
     * <pre> dic.services.serviceName1.instance = recreate </pre>
     */
    const CONFIG_VALUE_RECREATE = 'recreate';    
    
    /**
     * If <tt>__construct()</tt> has parameter $options ( it have to be first ), 
     * __construct() will retrived from Dic empty array in this parameter by default. 
     * But you can define options array in <tt>application.ini</tt>:
     * <pre>
     * dic.services.serviceName.options.key1 = val-1
     * dic.services.serviceName.options.key2[] = val-2_1
     * dic.services.serviceName.options.key2[] = val1-2_2
     * dic.services.serviceName.options.key3.arrayKey1 = val-3_1
     * dic.services.serviceName.options.key3.arrayKey2 = val-3_2
     * </pre>
     * Dic will assign $options:
     * <code>
     * array(
     *     'key1' => 'val-1',
     *     'key2' => array( 'val-2_1', 'val1-2_2'),
     *     'key3' => array( 
     *         'arrayKey1' => 'val-3_1',
     *         'arrayKey2' => 'val-3_1'
     *     )
     * )
     *</code>
     * See also {@see Zaboy_Abstract::setOptions()}
     */
    const CONFIG_KEY_OPTIONS = 'options'; 
    
    /**
     * You can instruct the Dic what kaind of Service have to be given 
     * for each parameter.
     * Example of use in <tt>application.ini</tt>:
     * <pre> 
     * dic.services.serviceIsHadParm.params.paramName1 = serviceForParam1 
     * ;you also have to describe Service with name 'serviceForParam1'
     * dic.services.serviceForParam1.class = One_Class_Name
     * </pre>
     * See also {@see getDefaultServiceConfig()}
     */   
    const CONFIG_KEY_PARAMS = 'params';
   
    /**
     * You can load some Service automaticly, just after Dic will be loaded.
     * Add to these services descriptions in <tt>application.ini</tt>:
     * <pre>
     * dic.services.serviceName1.autoload = true
     * </pre>
     * Default value for this key is FALSE.
     * See also {@see getDefaultServiceConfig()}
     */
    const CONFIG_KEY_AUTOLOAD = 'autoload';    
    
    /**
     * Return Default Config on that case if some keys is absent in <tt>application.ini</tt> 
     * 
     * Function return default configuration of service.
     * If some keys of config aren't defined in  <tt>application.ini</tt>,
     * configuration for these keys from returned array is used.
     * If any key of top level is present in <tt>application.ini</tt> - this
     * part of Default Config is ignored completely
     * 
     * @return array 
     */
    static function getDefaultServiceConfig();
 
}