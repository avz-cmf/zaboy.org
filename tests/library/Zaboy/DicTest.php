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
    protected $_minimalAadditionalConfig = 
<<<'INI'
resources.dic[] = 
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
     * @covers Zaboy_Dic::get
    */
    public function testGetObjectWithoutParams_ClassIsSpecified() {
        $this->loadDic($this->_minimalAadditionalConfig);
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
    public function testGetObjectWithoutParams_ClassIsNotSpecified() {
        $this->loadDic($this->_minimalAadditionalConfig);
        $this->setExpectedException('Zaboy_Dic_Exception');    
        $service = $this->object->get('serviceWithoutParams');
        /* @var $service Zaboy_Example_Service_WithoutParams */
    }     

    /**
     * @covers Zaboy_Dic::get
    */
    public function testGetSingletonServiceWithoutParams_ClassIsNotSpecifiedButDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.service.serviceWithoutParams.class = Zaboy_Example_Service_WithoutParams
INI1
        );
        $service = $this->object->get('serviceWithoutParams');
        /* @var $service Zaboy_Example_Service_WithoutParams */
                $this->assertEquals(
                'return ' . 'testValue',
                $service->getString('testValue')
        );
    }
    
    /**
     * @covers Zaboy_Dic::get
    */
    public function testGetSingletonServiceWithoutParams_SpecifiedAndDescribedClassesAreDifferent() {
        $this->loadDic(
<<<'INI1'
resources.dic.service.serviceWithoutParams.class = Zaboy_Example_Service_WithoutParams
INI1
        );
        $service = $this->object->get('serviceWithoutParams', 'Zaboy_Service');
        /* @var $service Zaboy_Example_Service_WithoutParams */
                $this->assertEquals(
                'Zaboy_Example_Service_WithoutParams',
                get_class($service)
        );
    }

    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetObjectWithOptionsOnly_NoOptionsSpecified() {
        $this->loadDic($this->_minimalAadditionalConfig);
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
    public function testGetSingletonServiceWithOptionsOnly_NoOptionsInConfig() {
        $this->loadDic(
<<<'INI1'
resources.dic.service.serviceWithOptionsOnly.class = Zaboy_Example_Service_WithOptionsOnly
INI1
        );
        $service = $this->object->get('serviceWithOptionsOnly');
        /* @var $service Zaboy_Example_Service_WithoutParams */
                $this->assertEquals(
                array(),
                $service->getAttribs()
        );
    }     


    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServiceWithOptionsOnly_MinimalArrayOptionsInConfig() {
        $this->loadDic(
<<<'INI1'
resources.dic.service.serviceWithOptionsOnly.class = Zaboy_Example_Service_WithOptionsOnly
resources.dic.service.serviceWithOptionsOnly.options[] = 
INI1
        );
        $service = $this->object->get('serviceWithOptionsOnly');
        /* @var $service Zaboy_Example_Service_WithoutParams */
                $this->assertEquals(
                array(0 =>''),         // string  "...options[] = " is equals array(0 =>'')
                $service->getAttribs()
        );
    }     
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServiceWithOptionsOnly_WithOptionsInConfig() {
        $this->loadDic(
<<<'INI1'
resources.dic.service.serviceWithOptionsOnly.class = Zaboy_Example_Service_WithOptionsOnly
    ;There is method setParam() - it will be call   
resources.dic.service.serviceWithOptionsOnly.options.param = paramValue
    ;There isn't method setAttribKey - it will save in attribs array
resources.dic.service.serviceWithOptionsOnly.options.attribKey = attribValue
INI1
        );
        $service = $this->object->get('serviceWithOptionsOnly');
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
    public function testGetObjectWitNotSpecifiedParam_() {
        $this->loadDic($this->_minimalAadditionalConfig);
        $this->setExpectedException('Zaboy_Dic_Exception');    
        $service = $this
                ->object
                ->get('serviceWithNotSpecifiedParam' , 'Zaboy_Example_Service_NotSpecifiedParam');
        /* @var $service Zaboy_Example_Service_NotSpecifiedParam */
    }    
    
   
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServiceWitNotSpecifiedParam_NotDescribedAndNotLoadedBefore() {
        $this->loadDic(
<<<'INI1'
resources.dic.service.serviceWithNotSpecifiedParam.class = Zaboy_Example_Service_NotSpecifiedParam
INI1
        );
        $this->setExpectedException('Zaboy_Dic_Exception');    
        $service = $this->object->get('serviceWithNotSpecifiedParam');
        /* @var $service Zaboy_Example_Service_NotSpecifiedParam */
    }        
   
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetObjectWitNotSpecifiedParam_ServiceWithSameNameAreDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.service.notSpecifiedParam.class = Zaboy_Example_Service_WithoutParams
INI1
        );
        $service = $this
                ->object
                ->get('serviceWithNotSpecifiedParam' , 'Zaboy_Example_Service_NotSpecifiedParam');
        /* @var $service Zaboy_Example_Service_NotSpecifiedParam */
        $this->assertEquals(
            'Zaboy_Example_Service_WithoutParams',
            get_class($service->_notSpecifiedParam)
        );   
    }
    
     /**
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServiceWitNotSpecifiedParam_ServiceWithSameNameAreDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.service.serviceWithNotSpecifiedParam.class = Zaboy_Example_Service_NotSpecifiedParam   
resources.dic.service.notSpecifiedParam.class = Zaboy_Example_Service_WithoutParams
INI1
        );
        $service = $this->object->get('serviceWithNotSpecifiedParam');
        /* @var $service Zaboy_Example_Service_NotSpecifiedParam */
        $this->assertEquals(
            'Zaboy_Example_Service_WithoutParams',
            get_class($service->_notSpecifiedParam)
        );   
    }    
    
}
