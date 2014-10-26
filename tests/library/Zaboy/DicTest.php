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
        $applicationIni = file_get_contents( APPLICATION_PATH . '/../tests/application/configs/application.ini' );
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
    public function testGetObjectWithoutParams() {
        $this->loadDic($this->_additionalConfig);
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
    public function testGetSingletonServiceWithoutParams() {
        $additionalConfig = 
<<<'INI1'
;INI TEST START
resources.dic.service.serviceWithoutParams.class = Zaboy_Example_Service_WithoutParams
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"
;INI TEST END
INI1
;       
        $this->loadDic($additionalConfig);   
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
    public function testGetObjectWithOptionsOnly_NoOptionsRetrived() {
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
    public function testGetSingletonServiceWithOptionsOnly_NoOptionsInConfig() {
        $additionalConfig = 
<<<'INI1'
;INI TEST START
resources.dic.service.serviceWithOptionsOnly.class = Zaboy_Example_Service_WithOptionsOnly
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"
;INI TEST END
INI1
;       
        $this->loadDic($additionalConfig); 
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
    public function testGetSingletonServiceWithOptionsOnly_MnimalArrayOptionsInConfig() {
        $additionalConfig = 
<<<'INI1'
;INI TEST START
resources.dic.service.serviceWithOptionsOnly.class = Zaboy_Example_Service_WithOptionsOnly
resources.dic.service.serviceWithOptionsOnly.options[] = 
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"
;INI TEST END
INI1
;       
        $this->loadDic($additionalConfig); 
        $service = $this->object->get('serviceWithOptionsOnly' , 'Zaboy_Example_Service_WithOptionsOnly');
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
        $additionalConfig = 
<<<'INI1'
;INI TEST START
resources.dic.service.serviceWithOptionsOnly.class = Zaboy_Example_Service_WithOptionsOnly
;There is method setParam() - it will be call   
resources.dic.service.serviceWithOptionsOnly.options.param = paramValue
;There isn't method setAttribKey - it will save in attribs array
resources.dic.service.serviceWithOptionsOnly.options.attribKey = attribValue
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
    public function testGetObjectWitNotSpecifiedParam_() {
        $additionalConfig = 
<<<'INI1'
;INI TEST START
resources.dic[] = 
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"
;INI TEST END
INI1;
        $this->loadDic($additionalConfig);
        $this->setExpectedException('Zaboy_Dic_Exception');    
        $service = $this->object->get('serviceWithNotSpecifiedParam' , 'Zaboy_Example_Service_NotSpecifiedParam');
        /* @var $service Zaboy_Example_Service_NotSpecifiedParam */
    }    
    
   
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetServiceWitNotSpecifiedParam_NotDescribedAndNotLoadedBefore() {
        $additionalConfig = 
<<<'INI1'
;INI TEST START
resources.dic.service.serviceWithNotSpecifiedParam.class = Zaboy_Example_Service_NotSpecifiedParam
pluginPaths.Zaboy_Application_Resource = "Zaboy/Application/Resource"
;INI TEST END
INI1;
        $this->loadDic($additionalConfig);
        $this->setExpectedException('Zaboy_Dic_Exception');    
        $service = $this->object->get('serviceWithNotSpecifiedParam' , 'Zaboy_Example_Service_NotSpecifiedParam');
        /* @var $service Zaboy_Example_Service_NotSpecifiedParam */
    }        
    
}
