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

require_once 'Zaboy/Service.php';

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
    protected $objectClass = 'Zaboy_Service';    

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
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp(); 
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
        $this->object = new $this->objectClass();      
        $optons = array('testKey' => 'testValue' );
        $this->object->setOptions($optons);
        $this->assertEquals(
                'testValue',
                $this->object->getAttrib('testKey')
        );
    }   
      
    /**
     * @covers Zaboy_Service::__construct()
     */
    public function testSetOptionsByConstruct() {
        $optons = array('testKey' => 'testValue' );
        $this->object = new $this->objectClass($optons);         
        $this->assertEquals(
                'testValue',
                $this->object->getAttrib('testKey')
        );
    }
 
    public function testInjectedServicesIsEmptyArray() {
        $this->object = new $this->objectClass(); 
        /* @var $this->object Zaboy_Service */
        $this->assertEquals(
                Array(),
                $this->object->getInjectedServicesNames()
        );                 
    }
    
 
    public function testInjectOneService() {
        $service1 = new stdClass();
        $this->object = new $this->objectClass(); 
        /* @var $this->object Zaboy_Service */
        $this->assertEquals(
                Array(),
                $this->object->getInjectedServicesNames()
        );                 
    }    
}
