<?php
/**
* Zaboy_DicTest
*
*<b>This is test for {@see Zaboy_Dic}</b>
*
* @category Test
* @package Test
* @copyright Zaboychenko Andrey
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

require_once 'Zaboy/Dic.php';
require_once 'Zaboy/Example/Service/WithoutParams.php';
require_once 'Zaboy/Example/Service/WithOptionsOnly.php';
require_once 'Zend/Application.php';
require_once 'Zend/Application/Bootstrap/Bootstrap.php';

/**
* Zaboy_DicTest
*
*<b>This is test for {@see Zaboy_Dic}</b>
*
* @category Test
* @package Test
* @copyright Zaboychenko Andrey
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @uses Zend Framework from Zend Technologies USA Inc.
*/
class Zaboy_DicTest extends PHPUnit_Framework_TestCase 
{
    /**
    * @var string
    */
    protected $objectClass = 'Zaboy_Dic';

    /**
    * @var Zaboy_Dic
    */
    protected $object;
    
    /**
     * @var Zend_Application 
     */
    public $_application; 
    
    /**
     *
     * @var Zend_Application_Bootstrap_Bootstrap 
     */
    public $_bootstrap;


    /**
    * This strings will be add to application.ini for testing
    *
    * @var string
    */
    protected $_additionalConfig = 
<<<'INI'
;INI TEST START
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"    
resources.dic[] = 
;INI TEST END
INI;
    
    /**
    * Sets up the fixture, for example, opens a network connection.
    */
    protected function _pathApplicationIni($additionalConfig) {
        //read application.ini
        $applicationIni = file_get_contents( APPLICATION_PATH . '/configs/application.ini' );
        //make changed application.ini
        $productionPosition = strpos($applicationIni, '[production]');
        //We will insert additional config after '[production]'
        if ($productionPosition === false) {die("There is not correct header [production] section");}
        $position = $productionPosition + strlen('[production]' . PHP_EOL) - 1;
        //This is inserting $additionalConfig
        $testApplicationIni = substr_replace($applicationIni, $additionalConfig . PHP_EOL, $position, 0); 
        //write new application.ini for test
        $filename = dirname(__FILE__) . '/applicationTest.ini';
        $f = fopen( $filename , "w" );
        fwrite( $f , $testApplicationIni );
        fclose( $f );
        return $filename;        
    }
    /**
    * Sets up the fixture, for example, opens a network connection.
    */
    protected function loadDic($additionalConfig) {   
        $filename = $this->_pathApplicationIni($additionalConfig);
        $this->_application = new Zend_Application( APPLICATION_ENV , $filename);
        global $application;
        $application = $this->_application;
        $this->_application->bootstrap();
        $this->_bootstrap = $this->_application->getBootstrap();
        var_dump('$this->bootstrap->getResource');
        $this->object = $this->_bootstrap->getResource('dic');
    }
    
    /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    */
    protected function setUp() {

    }
    
    /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    */
    protected function tearDown() {
    }

    /**
     * @covers Zaboy_Dic::get
    */
    public function testGetServiceWithoutParams() {
        $this->object = new $this->objectClass();  
        $service = $this->object->get('serviceWithoutParams' , 'Zaboy_Example_Service_WithoutParams');
        /* @var $service Zaboy_Example_Service_WithoutParams */
                $this->assertEquals(
                'return ' . 'testValue',
                $service->getString('testValue')
        );
    }  
    

    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetServiceWithOptionsOnly_NoOptionsInConfig() {
        $this->loadDic($this->_additionalConfig);
        $service = $this->object->get('serviceWithOptionsOnly' , 'Zaboy_Example_Service_WithOptionsOnly');
        /* @var $service Zaboy_Example_Service_WithoutParams */
                $this->assertEquals(
                array(),
                $service->getAttribs()
        );
    }    
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetServiceWithOptionsOnly_WithOptionsInConfig() {
        $additionalConfig = 
<<<'INI1'
;INI TEST START
;There is method setParam() - it will be call   
resources.dic.service.serviceWithOptionsOnly.options.param = paramValue
;There isn't method setAttribKey - it will save in attribs array
resources.dic.service.serviceWithOptionsOnly.options.attribKey = attribValue
;
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"
;INI TEST END
INI1;
        $this->loadDic($additionalConfig);
        $service = $this->object->get('serviceWithOptionsOnly' , 'Zaboy_Example_Service_WithOptionsOnly');
        /* @var $service Zaboy_Example_Service_WithoutParams */
        $this->assertEquals(
            'paramValue',
            $service->param
        );
        $this->assertEquals(
            'attribValue',
            $service->getAttrib('attribKey')
        );                
    } 
   
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetServiceWithDefaultOptions_WithOptionsInDefaultOptions() {
        $additionalConfig = 
<<<'INI1'
;INI TEST START
resources.dic[] = 
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"
;INI TEST END
INI1;
        $this->loadDic($additionalConfig);
        $service = $this->object->get('serviceWithDefaultOptions' , 'Zaboy_Example_Service_DefaultOptions');
        /* @var $service Zaboy_Example_Service_WithoutParams */
        $this->assertEquals(
            'test param value from $_defaultOptions',
            $service->param
        );
        $this->assertEquals(
            'test atrib value from $_defaultOptions',
            $service->getAttrib('attribKey')
        );                
    } 
   
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetServiceWithDefaultOptions_OptionsInConfigOverrideDefaultOptions() {
        $additionalConfig = 
<<<'INI1'
;INI TEST START
;There is method setParam() - it will be call
resources.dic.service.serviceOptionsInConfig.options.param = paramValueFromGonfig
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"
;INI TEST END
INI1;
        $this->loadDic($additionalConfig);
        $service = $this->object->get('serviceOptionsInConfig' , 'Zaboy_Example_Service_DefaultOptions');
        /* @var $service Zaboy_Example_Service_WithoutParams */
        $this->assertEquals(
            'paramValueFromGonfig',
            $service->param
        );
        $this->assertNull(
             $service->getAttrib('attribKey')               
        );                
    } 
}