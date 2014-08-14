<?php
/**
 * Zaboy_Service_Abstract
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  require_once 'Zaboy/Services/Interface.php';  
  
/**
 * Zaboy_Service_Abstract
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Services_Abstract extends Zaboy_Abstract implements Zaboy_Services_Interface
{
      /** 
      * @var array default Options
      * 
      * If $options haven't submitted via __construct - it is used
      */   
     protected $_defaultOptions = array();

    /**
    * Service constructor 
    * 
    * Constructor's params of Service must be (array $options, Servece_Class $servece1, Another_Servece_Class $servece2, ...)
    * or (Servece_Class $servece1, Another_Servece_Class $servece2, ...)) or just ()
    * 
    * param array
    * @return void
    */  
    public function __construct(array $options=array()) 
    {
        if (empty($options)) {
            $options = $this->_defaultOptions;  
        }    
        
        parent::__construct($options);
    }

    /**
     * @return Zend_Application_Bootstrap_Bootstrap
     */
    protected function _getBootstrap()
    {
        global $application;
        /* @var $application Zend_Application */
        $bootstrap = $application->getBootstrap();
        return $bootstrap;
    }     

      /**
      * @return Zaboy_Dic
      */
     protected function  _getDic() {
        $bootstrap = $this->_getBootstrap();
        $dic = $bootstrap->getResource('dic');     
        return $dic;
     }       
}