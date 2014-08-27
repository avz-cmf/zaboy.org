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
      * If you change this, rename self::setClass()
      * application.ini :  recurces.dic.class = Avz_Dic...
      */     
     const CONFIG_KEY_CLASS = 'class'; 

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
     public function setClass( $dicClass)
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
         $dic= new $dicClass($options);
         /** @var $dic Zaboy_Dic  */
         if (is_callable(array($dic, 'init'))) {
            $dic->init();             
         }
         return $dic;
     }
 }
