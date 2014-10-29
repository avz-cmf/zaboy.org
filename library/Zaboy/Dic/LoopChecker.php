<?php
/**
 * Zaboy_Dic_LoopChecker
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  
/**
 * Zaboy_Dic_LoopChecker
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_LoopChecker extends Zaboy_Abstract
{
    /*
     * array of services which are starting for check loop depends
     */
    private $_runningServices = array();

     /**
      * @param string
      * @return void
      */    
    public function loadingStart($serviceName)
    {
        if (isset($this->_runningServices[$serviceName])) {
             require_once 'Zaboy/Dic/Exception.php';
             throw new Zaboy_Dic_Exception("Loop in depends while load Service($serviceName) is detected"); 
         }
        $this->_runningServices[$serviceName] = true; 
    }   
    
    /**
      * @param string
      * @return void
      */    
    public function loadingFinished($serviceName)
    {
        $this->_runningServices[$serviceName] = null;
    }
}