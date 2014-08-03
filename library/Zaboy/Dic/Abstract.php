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