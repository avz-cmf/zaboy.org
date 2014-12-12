<?php
/**
 * Zaboy_Example_Dic_OptionalParams
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';

/**
 * Zaboy_Example_Dic_OptionalParams
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Example_Dic_OptionalParams extends Zaboy_Abstract
{
    public $specifiedOptionalParam;
    
    public $notSpecifiedOptionalParam;

    /**
     * Service constructor
     * 
     * @return void
     */
    public function __construct($notSpecifiedOptionalParam = null, Zaboy_Services $specifiedOptionalParam = null)
    {
        $this->specifiedOptionalParam = $specifiedOptionalParam;
        $this->notSpecifiedOptionalParam = $notSpecifiedOptionalParam;
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