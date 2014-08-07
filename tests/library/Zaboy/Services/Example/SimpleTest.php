<?php
/**
 * Zaboy_Services_Example_SimpleTest
 * 
 *<b>This is test for {@see Zaboy_Services_Example_Simple}</b>
 * 
 * @category   Test
 * @package    Test
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/Services/Example/Simple.php';

/**
 * Zaboy_Services_Example_SimpleTest
 * 
 *<b>This is test for {@see Zaboy_Services_Example_Simple}</b>
 * 
 * @category   Test
 * @package    Test
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Services_Example_SimpleTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var string
     */
    protected $objectClass = 'Zaboy_Services_Example_Options';    

    /**
     * @var Zaboy_Services_Example_Simple
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
        //read application.ini
        $applicationIni = file_get_contents( APPLICATION_PATH . '/configs/application.ini' );
        //make changed application.ini
        $productionPosition = strpos($applicationIni, '[production]' . PHP_EOL);
        if ($position === false) (die( "There is not correct header [production] section" ));
        $position = $productionPosition + strlen('[production]' . PHP_EOL) - 1;
        $testApplicationIni = substr_replace($applicationIni, $this->addApplicationIni . PHP_EOL, $position, 0); //insert string
        //write new application.ini for test
        $f = fopen( dirname(__FILE__) . '/applicationTest.ini', "w" );
        fwrite( $f , $testApplicationIni );
        fclose( $f );
        
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, dirname(__FILE__) . '/applicationTest.ini');
        
        $this->object = new $this->objectClass();   
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testSetDefaultOptionsInConstruct() {
        $this->object = new $this->objectClass();   
        $this->assertEquals(
                'test param value from $_defaultOptions',
                $this->object->param
        );
        $this->assertEquals(
                'test atrib value from $_defaultOptions',
                $this->object->getAttrib('test atrib')
        );                 
    }
    
    
    /**
     * @covers Zaboy_Services_Simple::setParam
     */
    public function testSetParam() {
        $this->object->setParam('testValue');
        $this->assertEquals(
                'testValue',
                $this->object->param
        );
    }

}
