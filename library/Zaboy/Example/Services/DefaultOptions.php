<?php
/**
 * Zaboy_Example_Services_DefaultOptions
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Services.php';

/**
 * Zaboy_Example_Services_DefaultOptions.
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Example_Services_DefaultOptions extends Zaboy_Services
{
    /**
     * 
     */   
    static protected $_defaultServiceConfig = 
        array(
            self::CONFIG_KEY_OPTIONS => 
                array(
                    'param' => 'test param value from $_defaultOptions',
                    'attribKey' => 'test atrib value from $_defaultOptions'
                )
        )
    ;
    
    /**
    *
    * @see Zaboy_Services::setOptions()
    * @see setParam()
    * @var mix
    */
    public $param;
    
    /**
     * 
     * @param mix $param
     */
    public function setParam($param)
    {
        var_dump($param);
        $this->param = $param;
    } 
}