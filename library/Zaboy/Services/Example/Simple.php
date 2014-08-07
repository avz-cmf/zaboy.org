<?php
/**
 * Zaboy_Services_Example_Simple
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Services/Abstract.php';
  require_once 'Zaboy/Services/Interface.php';  
  
/**
 * Zaboy_Services_Example_Simple
 * 
 * There are tests:  {@see Zaboy_Services_Example_SimpleTest}
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see Zaboy_Services_Example_SimpleTest
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Services_Example_Simple extends Zaboy_Services_Abstract implements Zaboy_Services_Interface
{
      /** 
      * @var array default Options
      * 
      * If $options haven't submitted via __construct - it is used
      */   
     protected $_defaultOptions = array(
         'param' => 'test param value from $_defaultOptions',
         'test atrib' => 'test atrib value from $_defaultOptions'
     );
     /**
      * 
      * @see Zaboy_Services_Abstract::setOptions()
      * @see setParam()
      * @var mix
      */
     public $param;
    
    /**
    * Service constructor 
    * 
    * Constructor without params<br>
  * Will use $this->_defaultOptions if object was inherited from {@see Zaboy_Services_Abstract} <br>
    * 
    * @return void
    */  
    public function __construct() 
    {
        parent::__construct(array());
    }
    
    /**
    * Service constructor 
    * 
    * Constructor without params<br>
    * Will use $this->_defaultOptions if object was inherited from Zaboy_Services_Abstract <br>
    * 
    * @return void
    */  
    public function setParam($param) 
    {
        $this->param = $param;
    }    
}