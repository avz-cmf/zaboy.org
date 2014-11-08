<?php
/**
 * Zaboy_Example_Dic_OptionalParams
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Services.php';

/**
 * Zaboy_Example_Dic_OptionalParams
 * 
 * You have to describe param in application.ini or service with same name 
 * have to be loaded before, but if Service Paramm is optional it will be load if
 * his class was described in application.ini
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Example_Dic_OptionalParams extends Zaboy_Services
{
    public $_specifiedOptionalParam;
    
    public $_notSpecifiedOptionalParam;

    /**
     * Service constructor
     * 
     * @return void
     */
    public function __construct($notSpecifiedOptionalParam = null, Zaboy_Services $specifiedOptionalParam = null)
    {
        $this->_specifiedOptionalParam = $specifiedOptionalParam;
        $this->_notSpecifiedOptionalParam = $notSpecifiedOptionalParam;
    }

    /**
     * Retrieve a single string
     *
     * @param  string 
     * @return string
     */
    public function getString($param)
    {
        return 'return ' . $param;
    }
}