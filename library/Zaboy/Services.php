<?php
/**
 * Zaboy_Services
 * 
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  
/**
 * Base class for Services
 * 
 * <b>General remarks about Service Object and Service Class</b>
 * 
 * There are 5 different entities: Service, Service Name, Service Object, Service Class and Servise Config<br>
 * 
 * <br><b>Service</b><br>
 * Service is PHP object (Service Object) which was loaded and registered in {@see Zaboy_Dic} via {@see Zaboy_Dic::get()} with Service Name<br>
 * It is possibly only if Service is described in Service Config<br>
 * 
 * <br><b>Service Config</b><br>
 * In most cases Service Config is a part of application.ini
 * <pre> 
 * resources.dic.myService.class = My_Service_Class
 * ; My_Service_Class is Service Class 
 * </pre>
 * Mor about rules of desrcribe Services in Service Config see {@link Zaboy_Dic_ServicesConfigs}
 * 
 * <br><b>Service Class</b><br>
 * There isn't any special interface for Service Classes.
 * Class of Service Object may be inherited from {@see Zaboy_Services} or not.
 * There are some requirements for (@see Zaboy_Services::__construct()} definition.<br>
 * Next variants is possibly:
 * <code> 
 * //constructor is allowed not to have parameters
 * function __construct()
 * 
 * //there is $options only. Param has to have name $options
 * function __construct(array $options)
 * 
 * //There are some object type parameters with specified class name  
 * function __construct(Class_Name $object1, Another_Class $object2, ...)
 * //or 
 * function __construct(array $options, Class_Name $object1,  Another_Class ...)
 * //the $options if it exist as parametr for __construct have to be a first parameter.
 * 
 * //There are some parameters which is Services with specified class name or not
 * function __construct(Service_Class $service1, Another_Service_Class $service2, ...)
 * function __construct(array $options, Service_Class $service1, Another_Service_Class ...)
 * 
 * //According to requirements for Service describe, class of Service have to 
 * //specified in Service Config. Because this reason, you may have not to notify classes
 * // for Services in parameters, but it is recommended for type control. 
 * function __construct(Service_Class $service1, $service2  ...)
 * function __construct(array $options, $service1, $service2  ...)
 * 
 * //And all together:
 * function __construct(array $options, Service_Class $service1, $service2, Class_Name $object1)
 * //In this case we must have in Services config:
 * </code>
 * <pre> 
 * resources.dic.service1.class = Service_Class
 * resources.dic.service2.class = Next_Service_Class
 * </pre>
 * 
 * In another words: only objects in parameters except array of options which 
 * has to be in first place or can be absent.<br>
 * 
 * You have to call 
 * <code>
 *         parent::__construct($options);
 * // or 
 *         parent::__construct();
 * </code> 
 * in your constructor<br>
 * 
 * <b>About optional parametrs</b><br>
 * All optional params will be ignored 
 *(NULL will be retrived instead of them in to consructor).<br>
 * 
 * <br><b>What is Service Name?</b><br>
 * It is just string - param for (@see Zaboy_Dic::get()}<br> 
 * 
 * <b>Object_Class::_defaultOptions</b><br>
 * If Service Object is inherited from {@see Zaboy_Services} it contains
 * {@see Zaboy_Services::_defaultOptions}<br>
 * If $options haven't submitted via __construct - _defaultOptions will be used.<br>
 * See {@see Zaboy_Services_Example_SimpleTest::testSetDefaultOptionsInConstruct()}<br>
 * If $options is not empty - _defaultOptions will not be used fully ( any part)<br>
 * See {@see Zaboy_Services_Example_OptionsTest::testSetDefaultOptionsInConstruct()}<br>
 * 
 * Also, you can define $options for Service in config.ini. 
 * See about it (@see Zaboy_Dic_ServicesConfigs} <br>
 * 
 * @todo getDefaultOptions() - to make
 * @todo Object_Class::_defaultOptions - rewrite docs
 * @todo Optional Params  - make method getOPtionalParam($lazyLoadedServiceName)
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Services extends Zaboy_Abstract
{
    /**
     * If $options haven't submitted via __construct() - it is used
     * 
     * @var array default Options
     */   
    protected $_defaultOptions = array();

    /**
     * Service constructor can use default options
     * 
     * @param array
     * @return void
     */  
    public function __construct(array $options=array()) 
    {
        if ($options === array()){
            $options = $this->_defaultOptions;
        }
        // See Zaboy_Abstract::setOptions(array $options) and  
        // See Zaboy_Abstract::setAttrib($key, $value)
        parent::__construct($options);
    }
}