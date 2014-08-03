<?php
/**
 * Zaboy_Dic_Service_Interface
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Interface Zaboy_Dic_Service_Interface - it is for serviceObjects
 * 
 * <br><b>What is serviceObject?</b><br>
 * It is object which can be load by (@link Zaboy_Dic::get()}<br>
 * There are requirements for (@link Zaboy_Dic_Service_Abstract::__construct}:<br>
 * Constructor's params must be (array $options, Servece_Class $servece1, Another_Servece_Class $servece2, ...)
 * or (Servece_Class $servece1, Another_Servece_Class $servece2, ...)) or just ()
 * in other words - $options or first on absent<br>
 * Also, you can describe object in config.ini. See about it (@link Zaboy_Dic_Abstract} <br>
 * 
 * <br><b>What is serviceName?</b><br>
 * It is string - param for (@link Zaboy_Dic::get()} and (@link Zaboy_Dic::has()}
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
interface Zaboy_Dic_Service_Interface
{    

}