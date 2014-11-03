<?php
/**
 * Zaboy_Dic_Abstract
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/Abstract.php';

/**
 * The base class for {@see Zaboy_Dic}
 * 
 * <b>Zaboy_Dic</b><br>
 * 
 * Zaboy_Dic is dependency injection container class (<i>Dic</i>). <i>Dic</i> is 
 * loading as resurce plugin of Bootstrap { @see Zaboy_Application_Resource_Dic}. 
 * 
 * Add in <i>application.ini</i> for load { @see Zaboy_Dic}<br>
 * <code> 
 * resources.dic[] = 
 * </code>
 * You can change class of {@see Zaboy_Dic} for load:
 * <code> 
 * resources.dic.class = My_Dic_Class 
 * </code>
 * For more information about configuration see {@see Zaboy_Dic_ServicesConfigs} 
 * and {@see Zaboy_Application_Resource_Dic}<br>  * 
 * 
 * You can get <i>Dic</i> as resource: <br>
 * <code> 
 *   $dic = $bootstrap->getResource('dic');
 * </code>
 * <br>
 * 
 * <b>Objects and Services for DIC</b><br>
 * Zaboy_Dic can load any object which corresponds a some requirements.<br>
 * Zaboy_Dic also can contane that objectif it is described in Services config 
 * ( in most cases it is application.ini).
 * In that case it is Service. <br>
 * For more information about Objects, Services and requirements for them, 
 * see {@see Zaboy_Service}
 * 
 * @category   Dic
 * @package    Dic
 * @see Zaboy_Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @
 */
class Zaboy_Dic_Abstract extends Zaboy_Abstract
{    
     
    /**
    * Title
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
    public function get($serviceName,$serviceClass = null){}
    
      /**
      * For usees in {@see Zaboy_Abstract::setOptions()} or load DIC without resurce plugin
      * 
      * @param array
      * @return void
      * /    
     public function setServicesConfig($servicesConfig);
     
      /**
      * @param string Name of service
      * @return array config for service with name = $serviceName or for all services if $serviceName === null
      * /    
     public function getServicesConfig($serviceName = null);

*/     
}
