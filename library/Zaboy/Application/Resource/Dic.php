<?php 
/**
  * Zaboy_Application_Resource_Dic
  * 
  * @category   Dic
  * @package    Dic
  * @copyright  Zaboychenko Andrey
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  */
 require_once 'Zend/Application/Resource/ResourceAbstract.php';
 require_once 'Zaboy/Dic.php';
 
 /**
  *Zaboy_Application_Resource_Dic
  * 
  * @category   Dic
  * @package    Dic
  * @copyright  Zaboychenko Andrey
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @uses Zend Framework from Zend Technologies USA Inc.
  */
  
 class Zaboy_Application_Resource_Dic extends Zend_Application_Resource_ResourceAbstract
 {
     /**
      * If you change this, rename self::setClassForDic()
      * application.ini :  recurces.dic.classForDic = Avz_Dic...
      */     
     const CONFIG_KEY_CLASS_FOR_DIC = 'classForDic'; 
     
    /**
     * comfig.ini :  
     * dic.services.serviceName.class = Same_Class
     * dic.services.serviceName.options.key = val 
     */
     const CONFIG_KEY_SERVICE = 'services';
     

     /**
      * Default class for dependency injection container
      */     
     const DEFAULT_CLASS_FOR_DIC = 'Zaboy_Dic';
 
      /** 
      * 
      * @see Zaboy_Application_Resource_Dic::DEFAULT_CLASS_FOR_DIC
      * @var class for dependency injection container
      */   
     protected $_dicClass = self::DEFAULT_CLASS_FOR_DIC;    //dic.class = Avz_Dic...

      /**
      * @param string
      * @return void
      */
     public function setClassForDic( $dicClass)
     {
         $this->_dicClass = $dicClass;
     }
     
     /**
      * Defined by Zend_Application_Resource_Resource
      *
      * @return Avz_Widgets
      */
     public function init()
     {
        $dicClass = $this->_dicClass;       
        $options = $this->getOptions();
        if (isset($options[self::CONFIG_KEY_SERVICE])) {
            $servicesConfigsOptions = $options[self::CONFIG_KEY_SERVICE];
            unset($options[self::CONFIG_KEY_SERVICE]);
        }else{
            $servicesConfigsOptions = Array();
        }
        
        $servicesConfigs = new Zaboy_Dic_ServicesConfigs($servicesConfigsOptions);
        $servicesStore = new Zaboy_Dic_ServicesStore($servicesConfigs);
        $loopChecker = new Zaboy_Dic_LoopChecker();     
        $factory = new Zaboy_Dic_Factory($servicesConfigs, $servicesStore, $loopChecker);        
        $dic= new $dicClass($options, $servicesConfigs, $servicesStore, $factory );
        /** @var $dic Zaboy_Dic  */
        
         return $dic;
     }
 }
