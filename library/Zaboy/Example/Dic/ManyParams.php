<?php
/**
 * Zaboy_Example_Dic_ManyParams
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Zaboy_Example_Dic_ManyParams.
 * 
 * @category   Example
 * @package    Example
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Example_Dic_ManyParams
{
    
    public $_specifiedParam;
    public $_notSpecifiedParam;
    public $_optionalParam;
    
    /**
    * 
    * @return void
    */  
    public function __construct(
            array $options = array(),
            Zaboy_Example_Dic_WithoutParams $specifiedParam,
            $notSpecifiedParam,
            Zaboy_Services $optionalParam = null
            
    ){   
        parent::__construct($options);
        $this->_specifiedParam = $specifiedParam;
        $this->_notSpecifiedParam = $notSpecifiedParam;        
        $this->_optionalParam = $optionalParam;        
    }

}