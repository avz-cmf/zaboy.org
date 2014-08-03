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
 * Zaboy_Dic is dependency injection container class (<i>Dic</i>). <i>Dic</i> is loading as resurce plugin of Bootstrap. Add in config.ini for load<br>
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
 * The <b>$options</b> ( if it exist as parametr for __construct ) must be first parametr. 
 * More about <b>$options</b> - see {@see Zaboy_Abstract::setOptions()}.<br>
 * You can note options for <i>Service</i>  in config.ini <br>
 * <code> 
 * resources.dic.service.NameOfService.options.param1 = value1
 * resources.dic.service.NameOfService.options.param2 = value2
 * </code>
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
 * Class will be load if resources.dic.service.WithAutoload.autoload = true, where 'WithAutoload' is services name
 *  - in config          resources.dic.service.WithAutoload.autoload = true
 * 
 * @category   Dic
 * @package    Dic
 * @see Zaboy_Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
interface Zaboy_Dic_Interface
{    
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
}