<?php
/**
 * Zaboy_Services_Example_DefaultOptions
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Service.php';

/**
 * Zaboy_Services_Example_DefaultOptions.
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Services_Example_DefaultOptions extends Zaboy_Service
{
    /**
    * @var array default Options
    *
    * If $options haven't submitted via __construct - it is used
    */
    protected $_defaultOptions = array(
    'param' => 'test param value from $_defaultOptions',
    'attribKey' => 'test atrib value from $_defaultOptions'
    );   
    
    /**
    *
    * @see Zaboy_Services::setOptions()
    * @see setParam()
    * @var mix
    */
    public $param;
    
    /**
    * Service constructor
    *
    * Constructor without $options<br>
    * Will use $this->_defaultOptions if object was inherited from {@see Zaboy_Services} <br>
    *
    * @return void
    */
    public function __construct($options = array())
    {
        parent::__construct($options);
    }
    
    /**
    * Service constructor
    *
    * Constructor without params<br>
    * Will use $this->_defaultOptions if object was inherited from Zaboy_Services <br>
    *
    * @return void
    */
    public function setParam($param)
    {
        $this->param = $param;
    } 
}