<?php
/**
 * Zaboy_Example_Service_NotSpecifiedParam
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Service.php';

/**
 * Zaboy_Example_Service_NotSpecifiedParam
 * 
 * You have to describe param in application.ini or service with same name 
 * have to be loaded before
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Example_Service_NotSpecifiedParam extends Zaboy_Service
{
    
    public $_notSpecifiedParam;
            
    /**
     * Service constructor
     * 
     * @return void
     */
    public function __construct($notSpecifiedParam)
    {
        $this->_notSpecifiedParam = $notSpecifiedParam;
    }

}