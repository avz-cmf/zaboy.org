<?php
/**
 * Zaboy_ServiceTest
 * 
 *<b>This is test for {@see Zaboy_Service}</b>
 * 
 * @category   Test
 * @package    Test
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/Services.php';
require_once 'Zaboy/Example/Services/DefaultOptions.php';

/**
 * Zaboy_ServiceTest
 * 
 *<b>This is test for {@see Zaboy_Service}</b>
 * 
 * @category   Test
 * @package    Test
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_ServiceTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var string
     */
    protected $objectClass = 'Zaboy_Services';    
    
    /**
     * @var string
     */
    protected $objectClassWithDefaultOptions = 'Zaboy_Example_Services_DefaultOptions';    
    
    /**
     * @var Zaboy_Service
     */
    protected $object;
    
    /**
     * This strings will be add to application.ini for testing
     * 
     * @var string
     */
    protected $addApplicationIni  = <<<'INI'
;INI TEST START 
;replase.this.string = 'to your options'
;INI TEST END
INI;
    
    /**
    * Sets up the fixture, for example, opens a network connection.
    */
    protected function _pathApplicationIni($additionalConfig) {
        //read application.ini
        $applicationIni = file_get_contents( APPLICATION_PATH . '/../tests/application/configs/application.ini' );
        //make changed application.ini
        $productionPosition = strpos($applicationIni, '[production]');
        //We will insert additional config after '[production]'
        if ($productionPosition === false) {die("There is not correct header [production] section");}
        $position = $productionPosition + strlen('[production]' . PHP_EOL) - 1;
        //This is inserting $additionalConfig
        $testApplicationIni = substr_replace($applicationIni,
                ';INI TEST START'
                . PHP_EOL
                . 'pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"'     
                . PHP_EOL
                . $additionalConfig 
                . PHP_EOL
                . ';INI TEST END'
                . PHP_EOL
        , $position, 0); 
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
     * @covers Zaboy_Service::setAttrib
     */
    public function testSetAtrib() {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $this->object = new $this->objectClass();         
        $this->object->setAttrib('Atrib', 'testValue');
        $this->assertEquals(
                'testValue',
                $this->object->getAttrib('Atrib')
        );
    }
     
    /**
     * @covers Zaboy_Service::setOptions
     */
    public function testSetOptions() {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $this->object = new $this->objectClass();      
        $options = array('testKey' => 'testValue' );
        $this->object->setOptions($options);
        $this->assertEquals(
                'testValue',
                $this->object->getAttrib('testKey')
        );
    }   
      
    /**
     * @covers Zaboy_Service::__construct()
     */
    public function testSetOptionsByConstruct() {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $options = array('testKey' => 'testValue' );
        $this->object = new $this->objectClass($options);         
        $this->assertEquals(
                'testValue',
                $this->object->getAttrib('testKey')
        );
    }
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetServiceWithDefaultOptions_WithOptionsInDefaultOptions() {
        $this->loadDic(
<<<'INI1'
;INI TEST START
resources.dic[] = 
;INI TEST END
INI1
        );
        $service = $this->object->get('serviceWithDefaultOptions' , $this->objectClassWithDefaultOptions);
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
resources.dic.services.serviceOptionsInConfig.class = Zaboy_Example_Services_DefaultOptions
resources.dic.services.serviceOptionsInConfig.options.param = paramValueFromGonfig
;INI TEST END
INI1;

        $this->loadDic($additionalConfig);

        $service = $this->object->get('serviceOptionsInConfig');
        $this->assertEquals(
            'paramValueFromGonfig',
            $service->param
        );
        $this->assertNull(
             $service->getAttrib('attribKey')               
        );                
    }     
}
