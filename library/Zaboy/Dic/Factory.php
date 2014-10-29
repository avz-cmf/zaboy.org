<?php
/**
 * Zaboy_Dic_Factory
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Dic/Abstract.php';
  require_once 'Zaboy/Dic/ServicesConfigs.php';
  require_once 'Zaboy/Dic/LoopChecker.php';
  require_once 'Zaboy/Dic/ServicesStore.php';
  
/**
 * Zaboy_Dic_Factory
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_Factory extends Zaboy_Abstract
{
    
    /*
     * @var Zaboy_Dic_ServicesConfigs
     */
    private $_servicesConfigs;

    /*
     * Object helps chekc loops in dependencies in the tree of services constructors
     * 
     * Zaboy_Dic_LoopChecker
     */
    private $_loopChecker;
    
    /**
     * 
     * @param array $options
     * @param Zaboy_Dic_ServicesConfigs $servicesConfigs
     * @param Zaboy_Dic_LoopChecker $loopChecker
     */
    public function __construct
    ( 
        array $options, 
        Zaboy_Dic_ServicesConfigs $servicesConfigs,
        Zaboy_Dic_LoopChecker $loopChecker        
    ){
        parent::__construct($options);     
        $this->_servicesConfigs = $servicesConfigs;
        $this->_loopChecker = $loopChecker;
    }  

    /**
     * instantiate service
     * 
     * @param type $serviceName
     * @return null
     */
    public function createService($serviceName)    
    {
        $serviceClass = $this->_servicesConfigs->getServiceClass($serviceName);

        $this->_loopChecker->loadingStart($serviceName);
        
       $reflectionObject = new ReflectionClass($serviceClass);
       
       $callParamsArray = $this->_getConstructParamsValues( $reflectionObject, $serviceName);
// it's like new class($callParamsArray[1], $callParamsArray[2]...)              
       $serviceInstance = $reflectionObject->newInstanceArgs($callParamsArray); 
       $this->_loopChecker->loadingFinished($serviceName);    
        return $serviceInstance;            
    }    


    /**
     * instantiate service
     * 
     * @param type $serviceName
     * @return null
     */
    public function createObject($objectClass)    
    {
        $this->_loopChecker->loadingStart($objectClass);
        
       $reflectionObject = new ReflectionClass($objectClass);    
       $callParamsArray = $this->_getConstructParamsValues( $reflectionObject);
        // it's like new class($callParamsArray[1], $callParamsArray[2]...)              
       $objectInstance = $reflectionObject->newInstanceArgs($callParamsArray); 

       
       $this->_loopChecker->loadingFinished($objectClass);        
        return $objectInstance;
    }

     /**
     * @param string 
     * @param /ReflectionObject
     * @return array  {@see ReflectionClass::newInstanceArgs()}
     */
    protected function _getConstructParamsValues( $reflectionObject, $serviceName= null)
    {
        $constructParams = array();
         //Get params for $className::__construct
        $reflectionConstruct = $reflectionObject->getMethod('__construct');
        $reflectionParams = $reflectionConstruct->getParameters();
        $options = $this->_resolveOptions($reflectionParams, $serviceName);
        if (isset($options)) {
            $constructParams[] = $options;
            array_shift($reflectionParams);
        }            
        foreach ($reflectionParams as $reflectionParam) {
            $constructParams[] = $this->_getConstructParamValue($reflectionParam, $serviceName);
        }
        return $constructParams;
    } 
    

     /**
     * @param string 
     * @param /ReflectionParameter
     * @return array  {@see ReflectionClass::newInstanceArgs()}
     */
    protected function _getConstructParamValue( $reflectionParam, $serviceName= null)    
    {
        if ($reflectionParam->isOptional()) {
            // optional params don't load
            $instance = null;
        }else{
            $constructParamService = $this
                ->_resolveServiceForConstructParam($reflectionParam, $serviceName);
            if (isset($constructParamService)) {
               $instance = $this->_getDic()->get($constructParamService, null);                   
            }else{                    
                $constructParamClassObject = $reflectionParam->getClass();
                if (!isset($constructParamClassObject)) {
                    require_once 'Zaboy/Dic/Exception.php';
                    throw new Zaboy_Dic_Exception(
                        "Cann't resolve class for param  - " . $reflectionParam->getName());
                }
                $constructParamClass = $constructParamClassObject->getName();
                $instance = $this->_getDic()->get(null, $constructParamClass); 
            }
        }
        return $instance;
    }
    
     /**
     * @param string 
     * @param /ReflectionObject
     * @return array  {@see ReflectionClass::newInstanceArgs()}
     */
    protected function _resolveOptions( $reflectionParams, $serviceName= null)
    {        
        //extract option (if exist) from array $reflectionParams
        $isOptionsInConstructParams = isset($reflectionParams[0]) 
            && $reflectionParams[0]->getName() === 'options';    
        if ($isOptionsInConstructParams) {
            if (isset($serviceName)) {
                $optionsFromConfig = $this->_servicesConfigs->getServiceOptions($serviceName);
                $options = $optionsFromConfig;
            }else{
                $options = array();
            }
        }else{
            $options = null;
        }           
        return $options;   
    }    
    
     /**
     * @param string 
     * @param /ReflectionObject
     * @return array  {@see ReflectionClass::newInstanceArgs()}
     */
    protected function _resolveServiceForConstructParam( \ReflectionParameter $reflectionParam, $serviceName)
    {   
        if (isset($serviceName)) {
            //Information about params from config for services is more
            //important then types was specified in __construct()
            $constructParamName = $reflectionParam->getName();
            $serviceForConstructParam = $this->_servicesConfigs
               ->getServiceForConstructParam($serviceName, $constructParamName);
        }else{
            $serviceForConstructParam = null;            
        } 
        return $serviceForConstructParam;

    }       
    
}