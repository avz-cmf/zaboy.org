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
  require_once 'Zaboy/Services/Interface.php';  
  
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

 * @todo Object_Class :: _defaultOptions - rewrite docs
 * @todo Optional Params  - make method getOPtionalParam($lazyLoadedServiceName)
 * @category   Services
 * @package    Services
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Services extends Zaboy_Abstract implements Zaboy_Services_Interface
{
    /**
     * 
     */   
    static protected $_defaultServiceConfig = 
        array(
            //self::CONFIG_KEY_INSTANCE => null,
            //self::CONFIG_KEY_OPTIONS => null,
            //self::CONFIG_KEY_PARAMS => null,
            //self::CONFIG_KEY_AUTOLOAD => null
        )
    ;
    
    /*
     * Array with constructors optional params
     * 
     * <code>
     * array() 
     * //if there isn't optional params in __construct
     * array( 'param1'=> null, 'param2'=> null) 
     * // if they present
     * or
     * array( 'param1'=> null, 'param2'=> object) 
     * //after $this->_optionalParams['param2'] = $dic->getOptionalParamValue($this, 'param2') 
     * </code>
     * 
     * @see _getOptionalParams() 
     * @var array|null
     */
    protected $_optionalParams;


    /**
     * 
     * @return array
     */
    static function getDefaultServiceConfig()
    {
        return static::$_defaultServiceConfig;
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
     protected function  _getDic() 
     {
        $bootstrap = $this->_getBootstrap();
        $dic = $bootstrap->getResource('dic');     
        return $dic;
     }       

    /**
     * Return array with costructor prams which are optional (except $options)
     * 
     * Return: 
     * <code>
     * array() 
     * //if there isn't optional params in __construct
     * array( 'param1'=> null, 'param2'=> null) 
     * // if they present
     * or
     * array( 'param1'=> null, 'param2'=> object) 
     * //after $this->_optionalParams['param2'] = $dic->getOptionalParamValue($this, 'param2') 
     * </code>
     * 
     * @param /ReflectionClass $reflectionClass
     * @return array
     */
    protected function _getOptionalParams()    
    {
        if (isset($this->_optionalParams)) {
            return $this->_optionalParams;   
        }
        $this->_optionalParams = array();
        $reflectionClass = new ReflectionClass(get_class($this));  
        //Get params for $className::__construct
        $reflectionMethods = $reflectionClass->getMethods();
        foreach ($reflectionMethods as $reflectionMethod) {
            if ($reflectionMethod->name === '__construct') {
                /* @var $reflectionConstruct /ReflectionMethod */                
                $reflectionParams = $reflectionMethod->getParameters();
                // $reflectionParams array of ReflectionParameter
                foreach ($reflectionParams as $reflectionParam) {
                    /* @var $reflectionParam /ReflectionParameter */
                    if ($reflectionParam->isOptional()) {
                        $paramName = $reflectionParam->getName();
                        if ($paramName !== 'options') {
                            $this->_optionalParams[$paramName] = '';
                        }        
                    }    
                }        
            }
        }
        return $this->_optionalParams;       
    }       

    /**
     * Set param after $_getOptionalParams initiate
     * 
     * @param string $name
     * @return void
     */
    protected function _setOptionalParam($name)    
    {
        $this->_getOptionalParams();
        $dic = $this->_getDic();
        try {
            $this->_optionalParams[$name] = $dic->getOptionalParamValue($this, $name);
        } catch (Exception $exc) {
            require_once 'Zaboy/Services/Exception.php';
            throw new Zaboy_Services_Exception(
                    __METHOD__ . '($name) throws Exception. $name = ' . $name,
                    0,
                    $exc
            );
        }
    }
    
    public function __get($name) 
    {
        $optionalParams = $this->_getOptionalParams();
        if (array_key_exists($name, $optionalParams)) {
            if (empty($optionalParams[$name])) {
                $this->_setOptionalParam($name); 
            }
            return $this->_optionalParams[$name];
        }else{
            require_once 'Zaboy/Services/Exception.php';
            throw new Zaboy_Services_Exception(
                "Wrong property $name in class" . get_class($this)
            ); 
        }
    }

    public function __isset($name) 
    {
        $isSetParam = isset($this->_optionalParams) 
            && !empty($this->_optionalParams[$name]);
        return $isSetParam;
    }

    public function __set($name, $value) 
    {
        require_once 'Zaboy/Services/Exception.php';
        throw new Zaboy_Services_Exception(
            "Don't try set property $name - just use it"
        ); 
    }
    
    public function __unset($name) 
    {
        require_once 'Zaboy/Services/Exception.php';
        throw new Zaboy_Services_Exception(
            "You cann't unset property $name"
        ); 
    }    
}