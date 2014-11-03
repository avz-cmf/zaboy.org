<?php
/**
 * Zaboy_Dic_LoopChecker
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
  
/**
 * Zaboy_Dic_LoopChecker
 * 
 * It check loop of dependencies in a running services
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_LoopChecker
{
    /*
     * array of services which are starting for check loop depends
     */
    private $_runningServices = array();

     /**
     * Add running service to {@see $_runningServices}
     * 
     * @param string
     * @return void
     */    
    public function loadingStart($name)
    {
        if (isset($this->_runningServices[$name])) {
             require_once 'Zaboy/Dic/Exception.php';
             throw new Zaboy_Dic_Exception("Loop in depends while load Service( $name ) is detected"); 
         }
        $this->_runningServices[$name] = true; 
    }   
    
    /**
     * Delete running service from {@see $_runningServices}
     * 
     * @param string
     * @return void
     */    
    public function loadingFinished($name)
    {
        unset( $this->_runningServices[$name]);
    }
}