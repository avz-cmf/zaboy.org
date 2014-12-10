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
require_once 'Zaboy/Example/Dic/ManyParams.php';
require_once 'Zaboy/Example/Dic/NotSpecifiedParam.php';
require_once 'Zaboy/Example/Dic/OptionalParams.php';
require_once 'Zaboy/Example/Dic/WithOptionsOnly.php';
require_once 'Zaboy/Example/Dic/WithSpecifiedParam.php';
require_once 'Zaboy/Example/Dic/WithoutParams.php';
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
    protected $_minimalAdditionalConfig = 
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
    public function testGetObjectsChild_WithConstructInParent() {
        $this->loadDic($this->_minimalAdditionalConfig);
        $service = $this->object->get('serviceChild' , 'Zaboy_Example_Dic_WithSpecifiedParam_Child');
        /* @var $service Zaboy_Example_Dic_WithSpecifiedParam_Child */
        $this->assertTrue   (
        is_object($service->getSpecifiedParam())
        );
    }   
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetObjects_WithoutConstruct() {
        $this->loadDic($this->_minimalAdditionalConfig);
        $service = $this->object->get('serviceWithoutConstruct' , 'Zaboy_Example_Dic_WithoutConstruct');
        /* @var $service Zaboy_Example_Dic_WithoutConstruct */
        $this->assertTrue   (
        is_object($service)
        );
    }   
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetObjectWithoutParams_ClassIsSpecified() {
        $this->loadDic($this->_minimalAdditionalConfig);
        $service = $this->object->get('serviceWithoutParams' , 'Zaboy_Example_Dic_WithoutParams');
        /* @var $service Zaboy_Example_Dic_WithoutParams */
        $this->assertEquals(
            'return ' . 'testValue',
            $service->getString('testValue')
        );
    } 
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetObjectWithoutParams_ClassIsNotSpecified() {
        $this->loadDic($this->_minimalAdditionalConfig);
        $this->setExpectedException('Zaboy_Dic_Exception');    
        $service = $this->object->get('serviceWithoutParams');
        /* @var $service Zaboy_Example_Dic_WithoutParams */
    }     

    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServiceWithoutParams_ClassIsNotSpecifiedButDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.serviceWithoutParams.class = Zaboy_Example_Dic_WithoutParams
INI1
        );
        $service = $this->object->get('serviceWithoutParams');
        /* @var $service Zaboy_Example_Dic_WithoutParams */
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
resources.dic.services.serviceWithoutParams.class = Zaboy_Example_Dic_WithoutParams
INI1
        );
        $service = $this->object->get('serviceWithoutParams', 'Zaboy_Service');
        /* @var $service Zaboy_Example_Dic_WithoutParams */
        $this->assertEquals(
            'Zaboy_Example_Dic_WithoutParams',
            get_class($service)
        );
    }

    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetObjectWithOptionsOnly_NoOptionsSpecified() {
        $this->loadDic($this->_minimalAdditionalConfig);
        $service = $this->object->get('serviceWithOptionsOnly' , 'Zaboy_Example_Dic_WithOptionsOnly');
        /* @var $service Zaboy_Example_Dic_WithoutParams */
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
resources.dic.services.serviceWithOptionsOnly.class = Zaboy_Example_Dic_WithOptionsOnly
INI1
        );
        $service = $this->object->get('serviceWithOptionsOnly');
        /* @var $service Zaboy_Example_Dic_WithoutParams */
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
resources.dic.services.serviceWithOptionsOnly.class = Zaboy_Example_Dic_WithOptionsOnly
resources.dic.services.serviceWithOptionsOnly.options[] = 
INI1
        );
        $service = $this->object->get('serviceWithOptionsOnly');
        /* @var $service Zaboy_Example_Dic_WithoutParams */
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
resources.dic.services.serviceWithOptionsOnly.class = Zaboy_Example_Dic_WithOptionsOnly
    ;There is method setParam() - it will be call   
resources.dic.services.serviceWithOptionsOnly.options.param = paramValue
    ;There isn't method setAttribKey - it will save in attribs array
resources.dic.services.serviceWithOptionsOnly.options.attribKey = attribValue
INI1
        );
        $service = $this->object->get('serviceWithOptionsOnly');
        /* @var $service Zaboy_Example_Dic_WithoutParams */
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
    public function testGetObjectWithNotSpecifiedParam_() {
        $this->loadDic($this->_minimalAdditionalConfig);
        $this->setExpectedException('Zaboy_Dic_Exception');    
        $service = $this
                ->object
                ->get('serviceWithNotSpecifiedParam' , 'Zaboy_Example_Dic_NotSpecifiedParam');
        /* @var $service Zaboy_Example_Dic_NotSpecifiedParam */
    }    
    
   
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServiceWithNotSpecifiedParam_NotDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.serviceWithNotSpecifiedParam.class = Zaboy_Example_Dic_NotSpecifiedParam
INI1
        );
        $this->setExpectedException('Zaboy_Dic_Exception');    
        $service = $this->object->get('serviceWithNotSpecifiedParam');
        /* @var $service Zaboy_Example_Dic_NotSpecifiedParam */
    }        
   
    /**
     * Object with not specified param, but service with same name exist
     * 
     * If class of praram is not specifed in __construct, 
     * there is only one way to point the class:
     * <code>
     * resources.dic.services.ServiceName.params.ParamName = AnotherServiceName
     * resources.dic.services.AnotherServiceName.class = ClassOfAnotherService
     * </code>
     * So, even if if Sevice with name identical name of param is described - 
     * it isn't enough
     * 
     * @covers Zaboy_Dic::get
     */
    public function testGetObjectWithNotSpecifiedParam_ServiceWithSameAsParamNameAreDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.notSpecifiedParam.class = Zaboy_Example_Dic_WithoutParams
INI1
        );
        $this->setExpectedException('Zaboy_Dic_Exception');   
        $service = $this
                ->object
                ->get('serviceWithNotSpecifiedParam' , 'Zaboy_Example_Dic_NotSpecifiedParam');
        /* @var $service Zaboy_Example_Dic_NotSpecifiedParam */
    }
    
   
    /**
     * Service with not specified param, but service with same name exist
     * 
     * If class of praram is not specifed in __construct, 
     * there is only one way to point the class:
     * <code>
     * resources.dic.services.ServiceName.params.ParamName = AnotherServiceName
     * resources.dic.services.AnotherServiceName.class = ClassOfAnotherService
     * </code>
     * So, even if if Sevice with name identical name of param is described - 
     * it isn't enough
     * 
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServiceWithNotSpecifiedParam_ServiceWithSameNameAreDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.serviceWithNotSpecifiedParam.class = Zaboy_Example_Dic_NotSpecifiedParam   
resources.dic.services.notSpecifiedParam.class = Zaboy_Example_Dic_WithoutParams
INI1
        );
        $this->setExpectedException('Zaboy_Dic_Exception');   
        $service = $this
                ->object
                ->get('serviceWithNotSpecifiedParam' , 'Zaboy_Example_Dic_NotSpecifiedParam');
        /* @var $service Zaboy_Example_Dic_NotSpecifiedParam */
    } 
   
    /**
     * Zaboy_Example_Dic_WithoutParams::__construct() 
     * Zaboy_Example_Dic_WithSpecifiedParam::__construct(Zaboy_Example_Dic_WithoutParams $specifiedParam) 
     * Zaboy_Example_Dic_NotSpecifiedParam::__construct($notSpecifiedParam)
     * 
     * @covers Zaboy_Dic::get
     * 
     */
    public function testGetSingletonServiceWithTwoLevelDependencies_FullDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.withoutParams.class = Zaboy_Example_Dic_WithoutParams
;    
resources.dic.services.specifiedParam.params.specifiedParam = withoutParams    
resources.dic.services.specifiedParam.class = Zaboy_Example_Dic_WithSpecifiedParam
;
resources.dic.services.notSpecifiedParam.params.notSpecifiedParam = specifiedParam
resources.dic.services.notSpecifiedParam.class = Zaboy_Example_Dic_NotSpecifiedParam
INI1
        );
        $service = $this->object->get('notSpecifiedParam');
        /* @var $service Zaboy_Example_Dic_NotSpecifiedParam */
        $this->assertEquals(
            'Zaboy_Example_Dic_WithoutParams',
            get_class($service->_notSpecifiedParam->getSpecifiedParam())
        );   
    }
    
    /**
     * Zaboy_Example_Dic_WithoutParams::__construct() 
     * Zaboy_Example_Dic_WithSpecifiedParam::__construct(Zaboy_Example_Dic_WithoutParams $specifiedParam) 
     * Zaboy_Example_Dic_NotSpecifiedParam::__construct($notSpecifiedParam)     * 
     * 
     * @covers Zaboy_Dic::get
     * 
     */
    public function testGetSingletonServiceWithTwoLevelDependencies_PartDescribed() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.srvc_specifiedParam.class = Zaboy_Example_Dic_WithSpecifiedParam
;
resources.dic.services.srvc_notSpecifiedParam.params.notSpecifiedParam = srvc_specifiedParam
resources.dic.services.srvc_notSpecifiedParam.class = Zaboy_Example_Dic_NotSpecifiedParam
INI1
        );     
        $service = $this->object->get('srvc_notSpecifiedParam');
        /* @var $service Zaboy_Example_Dic_NotSpecifiedParam */
        $this->assertEquals(
            'Zaboy_Example_Dic_WithoutParams',
            get_class($service->_notSpecifiedParam->getSpecifiedParam())
        );    
    }
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServices_isSingletonDefaultMethodOfLoad() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.srvc_WithSpecifiedParam_clone.class = Zaboy_Example_Dic_WithSpecifiedParam
  ; resources.dic.services.srvc_WithSpecifiedParam_clone.instance = singleton
INI1
        );     
        $service1 = $this->object->get('srvc_WithSpecifiedParam_clone');
        $service2 = $this->object->get('srvc_WithSpecifiedParam_clone');
        /* @var $service1 Zaboy_Example_Dic_WithoutParams */

        $this->assertTrue(
            $service1 === $service2
        );
    }    
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetSingletonServices() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.srvc_WithSpecifiedParam_clone.class = Zaboy_Example_Dic_WithSpecifiedParam
resources.dic.services.srvc_WithSpecifiedParam_clone.instance = singleton
INI1
        );     
        $service1 = $this->object->get('srvc_WithSpecifiedParam_clone');
        $service2 = $this->object->get('srvc_WithSpecifiedParam_clone');
        /* @var $service1 Zaboy_Example_Dic_WithSpecifiedParam */

        $this->assertTrue(
            $service1 === $service2
        );
        $this->assertTrue(
            $service1->getSpecifiedParam() === $service2->getSpecifiedParam()
        );          
    }
    
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetClonedServices() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.srvc_WithSpecifiedParam_clone.class = Zaboy_Example_Dic_WithSpecifiedParam
resources.dic.services.srvc_WithSpecifiedParam_clone.instance = clone
INI1
        );     
        $service1 = $this->object->get('srvc_WithSpecifiedParam_clone');
        $service2 = $this->object->get('srvc_WithSpecifiedParam_clone');
        /* @var $service1 Zaboy_Example_Dic_WithSpecifiedParam */
        
        $this->assertTrue(
            $service1 == $service2
        );
      
        
        $this->assertFalse(
            $service1 === $service2
        );
        $this->assertTrue(
            $service1->getSpecifiedParam() === $service2->getSpecifiedParam()
        );          
    } 
   
    /**
     * @covers Zaboy_Dic::get
     */
    public function testGetRecreatedServices() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.srvc_WithSpecifiedParam_clone.class = Zaboy_Example_Dic_WithSpecifiedParam
resources.dic.services.srvc_WithSpecifiedParam_clone.instance = recreate
INI1
        );     
        $service1 = $this->object->get('srvc_WithSpecifiedParam_clone');
        $service2 = $this->object->get('srvc_WithSpecifiedParam_clone');
        /* @var $service1 Zaboy_Example_Dic_WithSpecifiedParam */
        
        $this->assertTrue(
            $service1 == $service2
        );
        $this->assertTrue(
            $service1->getSpecifiedParam() == $service2->getSpecifiedParam()
        );        
        
        $this->assertFalse(
            $service1 === $service2
        );    
         $this->assertFalse(
            $service1->getSpecifiedParam() === $service2->getSpecifiedParam()
        );  
    } 
    
    /**
     * Zaboy_Example_Dic_WithoutParams::__construct() 
     * Zaboy_Example_Dic_WithSpecifiedParam::__construct(Zaboy_Example_Dic_WithoutParams $specifiedParam) 
     * Zaboy_Example_Dic_NotSpecifiedParam::__construct($notSpecifiedParam)     * 
     * 
     * @covers Zaboy_Dic::get
     * 
     */
    public function test_autoloadServices_loadSigletons() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.srvc_specifiedParam_Autoload.autoload = true
resources.dic.services.srvc_specifiedParam_Autoload.class = Zaboy_Example_Dic_WithSpecifiedParam
resources.dic.services.srvc_withoutParam_Autoload.autoload = true
resources.dic.services.srvc_withoutParam_Autoload.class = Zaboy_Example_Dic_WithoutParams
;
resources.dic.services.srvc_withoutParam_notAutoload.autoload = false
resources.dic.services.srvc_withoutParam_notAutoload.class = Zaboy_Example_Dic_WithoutParams
  ;  resources.dic.services.srvc_specifiedParam_notAutoload.autoload = false
resources.dic.services.srvc_specifiedParam_notAutoload.class = Zaboy_Example_Dic_WithSpecifiedParam
INI1
        );     
        $specifiedParamA = $this->object->getRunningServiceInstance("srvc_specifiedParam_Autoload");
        $withoutParamA = $this->object->getRunningServiceInstance("srvc_withoutParam_Autoload");        
        $this->assertEquals(
            true,
            isset($specifiedParamA) && isset($withoutParamA)
        ); 
        
        $specifiedParamNA = $this->object->getRunningServiceInstance("srvc_withoutParam_notAutoload");
        $withoutParamNA = $this->object->getRunningServiceInstance("srvc_specifiedParam_notAutoload");
        $this->assertEquals(
            false,
            isset($specifiedParamNA) || isset($withoutParamNA)               
            
        );           
    }    

    /**
     * @covers Zaboy_Dic::getRunningServiceName()
     */
    public function testGetRunningServiceName() {
        $this->loadDic(
<<<'INI1'
resources.dic.services.srvc_WithSpecifiedParam.class = Zaboy_Example_Dic_WithSpecifiedParam
INI1
        );     
        $service1 = $this->object->get('srvc_WithSpecifiedParam');
        /* @var $service1 Zaboy_Example_Dic_WithSpecifiedParam */
        
        $this->assertEquals(
            'srvc_WithSpecifiedParam',
            $this->object->getRunningServiceName($service1)
        );
    }    
}
