<?php
/**
 * Zaboy_Example_Service_WithoutParams
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Zaboy_Example_Service_WithoutParams.
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Example_Service_WithSpecifiedParam
{
    
    public $_specifiedParam;
    
    /**
    * 
    * @return void
    */  
    public function __construct(Zaboy_Example_Service_WithoutParams $specifiedParam) 
    {   
        $this->_specifiedParam = $specifiedParam;
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