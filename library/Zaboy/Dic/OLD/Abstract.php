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
 * Zaboy_Dic_Abstract
 * 
 * Dependency Injection Container
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @link /www/docs/class-Zaboy_Dic_Abstract.html
 * @link /docs/class-Zaboy_Dic_Abstract.html
 * @link class-Zaboy_Dic_Abstract.html
 * @link ../class-Zaboy_Dic_Abstract.html
 * @link https://github.com/avz-cmf/zaboy.org/wiki
 * @example /docs/class-Zaboy_Dic_Abstract.html
 * @example class-Zaboy_Dic_Abstract.html
 * @example http://_zaboy.org/docs/
 * @examples http://zaboy.org
 * @example http://zaboy.org/docs/
 * @examples ya.ru
 * @example https://github.com/apigen/apigen
 * @exam https://translate.google.com.ua/#en/ru/The%20%40covers%20annotation%20can%20be%20used%20in%20the%20test%20code%20to%20specify%20which%20method%28s%29%20a%20test%20method%20wants%20to%20test.
 */
class Zaboy_Dic_Abstract extends Zaboy_Abstract
{
     /**
      * If you change this, rename self::setService()
      * comfig.ini :  
      * dic.service.name = serviceNameOrAlias //optional
      * dic.service.class = Same_Class
      * dic.service.options.key = val 
      */
     const CONFIG_KEY_SERVICE = 'service';   //  comfig.ini :  dic.service.serviceName1.class = className1 ...  
     const CONFIG_KEY_CLASS = 'class';       //  comfig.ini :  dic.service.serviceName1.class = className1 ... 
     const CONFIG_KEY_OPTIONS = 'options';   //  comfig.ini :  dic.service.serviceName1.options.key = val  ...
     const CONFIG_KEY_AUTOLOAD = 'autoload'; //  comfig.ini :  dic.service.serviceName1.autoload = true
     
    /**
     * @var array
     */
    protected $_servicesConfig = array();
    
    /**
    * 
    * @see $_options
    * param array <b>options</b> - options from config.ini. <br>It is all after  <i>resources.dic.</i>
    * @return void
    */  
    public function __construct( array $options=array() ) 
    {    
        if (!empty($options))
        {
            $this->setOptions($options);
        }    
        return;
    }

      /**
      * @param array
      * @return void
      */    
     public function setService($servicesConfig)
     {
         $this->_servicesConfig = $servicesConfig;
     }

}