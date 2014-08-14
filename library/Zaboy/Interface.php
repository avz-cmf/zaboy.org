<?php
/**
 * Zaboy_Interface
 * 
 * @category   Zaboy
 * @package    Zaboy
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Interface Zaboy_Interface
 * 
 * 
 * @method  has($serviceName);
 * @category   Zaboy
 * @package    Zaboy
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
interface Zaboy_Interface
{    
    /**
     * 
     * @see $_options
     * param array <b>options</b> - options from config.ini. <br>It is all after  <i>resources.dic.</i>
     * @return void
     */  
    public function __construct(array $options=array());    
    
   
     /**
      * Call setters for elements $options if exist and rest copy to {@see Zaboy_Abstract::_attribs}
      * 
      * May be two cases for property $options['oneProperty'] = value
      * If method setOneProperty is exist - it will be call, else $this->_attribs['oneProperty'] = value
      *
      * @param  array $options
      * @return Zaboy_Abstract
      */
    public function setOptions(array $options);    
}
