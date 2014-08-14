<?php
/**
 * Zaboy_Dic_Interface
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Interface Zaboy_Dic_Interface
 * 
 *  <b>Zaboy_Dic</b>
 * 
 * Zaboy_Dic is dependency injection container class (<i>Dic</i>). <i>Dic</i> is loading as resurce plugin of Bootstrap. Add in <i>config.ini</i> for load<br>
 * <code> 
 * resources.dic[] = 
 * </code>
 * You can get <i>Dic</i>: <br>
 * <code> 
 *   $bootstrap->bootstrap('dic');
 *   $dic = $bootstrap->getResource('dic');
 * </code>
 * <br>
 * It can load and contane any object which implements {@see Zaboy_Dic_Service_Interface}.
 * That object we call <i>Service</i>. Specific of <i>Service</i> is parametrs of it's __consruct. <br>
 * The <b>$options</b> ( if it exist as parametr for __construct ) have to first parametr. 
 * More about <i>__consruct()</i> - see {@see Zaboy_Services_Abstract::__consruct()}.<br>
 * More about <i>$options</i> - see {@see Zaboy_Abstract::setOptions()}.<br>
 * You can note options for <i>Service</i>  in config.ini <br>
 * <code> 
 * resources.dic.service.NameOfService.options.param1 = value1
 * resources.dic.service.NameOfService.options.param2 = value2
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
 * resources.dic.service.NameOfService.class = New_Class_For_NameOfService
 * </code>
 * 
 * <b>About optional parametrs</b><br>
 * Optional  parametrs are transmitted to _consruct if they already was loaded or if they is discribed in config.ini
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
 * Class will be load if resources.dic.service.WithAutoload.autoload = true, where 'WithAutoload' is services name <br>
 * 
 * <br><b>What is NameOfService?</b><br>
 * It is string - param for (@see Zaboy_Dic::get()} and (@see Zaboy_Dic::has()}<br>
 * See more about it : (@see Zaboy_Dic_Interface)
 * 
 * @method  has($serviceName);
 * @category   Dic
 * @package    Dic
 * @see Zaboy_Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @
 */
interface Zaboy_Dic_Interface
{    
   /**
     * comfig.ini :
     * <pre>   
     *dic.service.serviceName1.class = ServiceClass1
     *dic.service.serviceName1.options.key11 = val11
     *dic.service.serviceName1.options.key12 = val12        
     *dic.service.serviceName1.autoload = true       //optional, by default is FALSE
     *;
     *dic.service.serviceName2.class = ServiceClass2
     *dic.service.serviceName2.options.key21 = val21
     *dic.service.serviceName2.options.key22 = val22 
     * </pre>   
     */
    const CONFIG_KEY_SERVICE = 'service';   //  comfig.ini :  dic.service... =  ...  
     
    /**
     * 
     * comfig.ini :
     * <pre>   
     * dic.service.serviceName1.class = ServiceClass1
     * </pre>   
     * @see CONFIG_KEY_SERVICE
     */
     const CONFIG_KEY_CLASS = 'class';
     
    /**
     * 
     * comfig.ini :
     * <pre>   
     *dic.service.serviceName1.options.key11 = val11
     *dic.service.serviceName1.options.key12 = val12    
     * </pre>   
     * @see CONFIG_KEY_SERVICE
     */     
     const CONFIG_KEY_OPTIONS = 'options';
     
    /**
     * 
     * comfig.ini :
     * <pre>   
     *dic.service.serviceName1.autoload = true  //optional, by default is FALSE
     * </pre>   
     * @see CONFIG_KEY_SERVICE
     */
     const CONFIG_KEY_AUTOLOAD = 'autoload';

    /**
     * 
     * @see $_options
     * param array <b>options</b> - options from config.ini. <br>It is all after  <i>resources.dic.</i>
     * @return void
     */  
    public function __construct(array $options=array());      
     
    /**
      * @param string
      * @return bool
      */    
    public function has($serviceName);
    
    
   /**
    * Return class by service name
    * 
    * return class of loaded service if it is already loaded or
    * if it isn't loaded, but if it is described ( for example in config.ini) -
    * return class from description
    * else return null
    * 
    * @param string
    * @return string Class of service or null
    */    
    public function getClass($serviceName);
            
    /**
    * 
    * Class in config is more imoprtant then class in __constract
    * <code>
    *    $dic->get('NameOrAlias'); //if class isn't noted in cofig.ini - exception
    *    //or
    *    $dic->get('NameOrAlias', 'Service_Class'); //if class is noted in cofig.ini, parameter  'Service_Class' ignore.   
    * </code>
    * <i>Service</i> which was loaded is containing in  <i>Dic</i> and can't is load again. <br>
    * 
    * @see http://stackoverflow.com/questions/1935771/how-to-use-call-user-func-array-with-an-object-with-a-construct-method-in-php
    * @param string
    * @param string
    * @return object
    */    
    public function get($serviceName,$serviceClass = null) ;
    
      /**
      * For usees in {@seeZaboy_Abstract::setOptions()} or load DIC without resurce plugin
      * 
      * You can do it only once. Usialy $servicesConfig is part of DIC config in application.ini<br>
      * with key which is defined in {@see CONFIG_KEY_SERVICE}<br>
      * Part of application.ini:<br>
      *<pre>
      * dic.service.serviceName.class = ServiceClass
      * dic.service.serviceName.options.key11 = val11
      * dic.service.serviceName.options.key12 = val12
      * dic.service.serviceName.autoload = true
      * dic.service.nextServiceName.class = NextServiceClass 
      * ...
      *</pre>
      * It is in $servicesConfig
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
      * @param array
      * @return void
      */    
     public function setServicesConfig($servicesConfig);
     
      /**
      * @param string Name of service
      * @return array config for service with name = $serviceName or for all services if $serviceName === null
      */    
     public function getServicesConfig($serviceName = null);     
}
