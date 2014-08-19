<?php
/**
 * Zaboy_Services_Example_OptionsTest
 * 
 *<b>This is test for {@see Zaboy_Services_Example_Options}</b>
 * 
 * @category   Test
 * @package    Test
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/Services/Example/Options.php';

/**
 * Zaboy_Services_Example_OptionsTest
 * 
 *<b>This is test for {@see Zaboy_Services_Example_Options}</b>
 * 
 * @category   Test
 * @package    Test
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Services_Example_OptionsTest extends PHPUnit_Framework_TestCase {

    /**
     * @var string
     */
    protected $objectClass = 'Zaboy_Services_Example_Options';

    /**
     * @var Zaboy_Services_Example_Simple
     */
    protected $object;
    
    
    /**
     * @var Zaboy_Services_Example_Simple
     */
    protected $_options = array(
         'param' => 'test param value from $_options',
         'options atrib' => 'test atrib value from $_options'
    );
    
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

    public function testSetDefaultOptionsInConstruct() {
        $this->object = new $this->objectClass($this->_options);    
        $this->assertEquals(
                'test param value from $_options',
                $this->object->param
        );
        $this->assertEquals(
                'test atrib value from $_options',
                $this->object->getAttrib('options atrib')
        );                 
    }
    
    
    /**
     * @covers Zaboy_Services_Simple::setParam
     */
    public function testSetParam() {
        $this->object = new $this->objectClass($this->_options);  
        $this->object->setParam('testValue');
        $this->assertEquals(
                'testValue',
                $this->object->param
        );
    }

}
