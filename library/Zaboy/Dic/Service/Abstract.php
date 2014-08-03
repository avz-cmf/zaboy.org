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
  require_once 'Zaboy/Dic/Service/Interface.php';  
  
/**
 * Zaboy_Service_Abstract
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_Service_Abstract extends Zaboy_Abstract implements Zaboy_Dic_Service_Interface
{
      /** 
      * @var array default Options
      * 
      * If $options haven't submitted via __construct - it is used
      */   
     protected $_defaultOptions = array();
     
    /**
     * self  name like 'svName' or 'wsRunner'. It is called from Dic.
     * 
     * @var string 
     */
     public $serviceName;




     /**
    * Service constructor 
    * 
    * Constructor's params must be (array $options, Servece_Class $servece1, Another_Servece_Class $servece2, ...)
    * or (Servece_Class $servece1, Another_Servece_Class $servece2, ...)) or just ()
    * in other words - $options or first or absent
    * 
    * @todo $options may be not first parametr
    * @see $_options
    * param array
    * @return void
    */  
    public function __construct($options = null) 
    {
        $argsArray =array();
        $numArgs = func_num_args();
        if ( $numArgs > 0 ){
            $argsArray = func_get_args();
            $mayBeOptions = func_get_arg(0);
            // if first param is array - we think it is options
            if (is_array($mayBeOptions)) {
                $options = array_shift($argsArray);
            }
        }
        if (empty($options)) {
            $options = $this->_defaultOptions;  
        }    
        
        parent::__construct($options);
          
    }

    public function setServiceName( $serviceName)
    {
        $this->serviceName = $serviceName;
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