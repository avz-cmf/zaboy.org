<?php
/**
 * Zaboy_Example_Services_OptionalParams
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Services.php';

/**
 * Zaboy_Example_Services_OptionalParams.
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Example_Services_OptionalParams extends Zaboy_Services
{
    /**
     * 
     * @param object|null $notSpecifiedParam
     * @param stdClass|null $specifiedParam
     */
    public function __construct($notSpecifiedParam = null, stdClass $specifiedParam = null) 
    {
        parent::__construct();
    }
    
}