<?php
/**
 * Zaboy_Services_Interface
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * <b>Interface Zaboy_Services_Interface</b> - it is Interface for Service Objects classes
 * 
 * There are 4 different entities: Service, Service Name, Service Object, Class of Service Object<br>
 * <br><b>Service</b><br>
 * Service is resurse (Service Objec) which was loaded and registered in {@see Zaboy_Dic} via {@see Zaboy_Dic::get()}  with Service Name<br>
 * Service =  Service Name +  Service Object <br>
 * More about <ul><li>Service</li> <li>Service Object </li> <li>Service Name</li> <li>Zaboy_Dic</li> </ul>see there: {@see Zaboy_Dic_Interface}<br>
 * 
 * <b>Service Object class</b><br>
 * Class of Service Object may be is inherited from {@see Zaboy_Services_Abstract} or not
 * There are requirements for (@see Zaboy_Services_Abstract::__construct()}:<br>
 * 
 * <b>Object_Class::__construct()</b><br>
 * Constructor's params must be: 
 * <ul>
 * <li>(array $options, Servece_Class $servece1, Another_Servece_Class $servece2, ...) or</li>
 * <li>(Servece_Class $servece1, Another_Servece_Class $servece2, ...)) or </li>
 * <li>(array $options). See {@see Zaboy_Services_Example_Options}.  Or just </li>
 * <li> public function __construct(). See {@see Zaboy_Services_Example_Simple}</li> 
 * </ul>
 * <i>$options</i> have to first or absent<br>
 * You have to call 
 * <code>
 *         parent::__construct($options);
 * // or 
 *         parent::__construct();
 * </code> 
 * in your constructor if parent::__construct() exist.<br>
 * 
 * <b>Object_Class::_defaultOptions</b><br>
 * If Service Object is inherited from {@see Zaboy_Services_Abstract} it contains
 * {@see Zaboy_Services_Abstract::_defaultOptions}<br>
 * If $options haven't submitted via __construct - _defaultOptions will be used.<br>
 * See {@see Zaboy_Services_Example_SimpleTest::testSetDefaultOptionsInConstruct()}<br>
 * If $options is not empty - _defaultOptions will not be used fully ( any part)<br>
 * See {@see Zaboy_Services_Example_OptionsTest::testSetDefaultOptionsInConstruct()}<br>
 * 
 * Also, you can describe object and $options for this object in config.ini. See about it (@see Zaboy_Dic_Interface} <br>
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
interface Zaboy_Services_Interface
{    

}