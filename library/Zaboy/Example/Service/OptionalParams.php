<?php
/**
 * Zaboy_Example_Service_OptionalParams
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Service.php';

/**
 * Zaboy_Example_Service_OptionalParams
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
class Zaboy_Example_Service_OptionalParams extends Zaboy_Service
{
    /**
     * Service constructor
     * 
     * @return void
     */
    public function __construct($optionalParam = null, Zaboy_Service $specifiedOptionalParam = null)
    {
        parent::__construct($optionalParam, $specifiedOptionalParam);
    }

}