<?php
/**
 * Zaboy_Dic_ServiceReflector
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

  require_once 'Zaboy/Abstract.php';
  
/**
 * Zaboy_Dic_ServiceReflector
 * 
 * @category   Dic
 * @package    Dic
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Dic_ServiceReflector extends Zaboy_Abstract
{
    /*
     * string class name for extract information about _construct()'s parameters
     */
    private $_serviceClass;
    
    /*
     * string service name for extract information about _construct()'s parameters
     */
    private $_serviceName;
    
    /*
     * Zaboy_Dic_ServicesConfigs  service name for extract information about _construct()'s parameters
     */
    private $_servicesConfigs;    
    
    /*
     * Zaboy_Dic_ServicesStore  service for extract information about SErvices, which are already loaded
     */
    private $_servicesStore ;       
     
    /*
     * Zaboy_Dic for recursive calls
     */
    private $_dic ;    
    
    /*
     * array array( int => value)
     */
    private $_constructParams = array();
    
    /**
     * 
     * param string class name for extract information about _construct()'s parameters
     * @return void
     */  
    public function __construct( $serviceName, $serviceClass, Zaboy_Dic_ServicesConfigs $servicesConfigs, Zaboy_Dic_ServicesStore $servicesStore, Zaboy_Dic $_dic ) 
    {
        $this->_serviceName = $serviceName;        
        $this->_serviceClass = $serviceClass;
        $this->_servicesConfigs = $servicesConfigs;
        $this->_servicesStore = $servicesStore;
        $this->_dic = $_dic;
        
        //Get params for $className::__construct
        $reflectionConstruct = $reflectionObject->getMethod('__construct');
        $reflectionParams = $reflectionConstruct->getParameters();
        //extract option (if exist) from array $reflectionParams
        $reflectionParams = $this->_resolveOptions($reflectionParams);
        foreach ($reflectionParams as $reflectionParam) {
            /** @var $reflectionParam \ReflectionParameter  */
            if ($reflectionParams->getClass()!== null) {
                $constructParamClass = $reflectionParams->getClass()->getName();
            }else{
                $constructParamClass = null;
            }
            $constructParamName = $reflectionParam->getName();
            $serviceObject = $this->_dic->get($constructParamName, $constructParamClass);
            $this->_constructParams[] = $serviceObject;
        }    
    }
     
    /**
     * @param string 
     * @param string /ReflectionObject
     * @param array $callParamInfo 
     */
    public function getConstructParamsValues($serviceName, $reflectionObject)
    {
        //Get params for $className::__construct
        $reflectionConstruct = $reflectionObject->getMethod('__construct');
        $reflectionParams = $reflectionConstruct->getParameters();
        //extract option (if exist) from array $reflectionParams
        if (isset($reflectionParams[0])) {
            $mayBeOptionsParam = $reflectionParams[0];
            /** @var $mayBeOptionsParam \ReflectionParameter  */
            $mayBeOptionsParamName = $mayBeOptionsParam->getName();
            if ($mayBeOptionsParamName === 'options') {
                $optionsFromConfig = $this->_servicesConfigs->getServiceOptions($serviceName);
                $constructParams = $optionsFromConfig;
                array_shift($reflectionParams);               
            }           
        }
        foreach ($reflectionParams as $reflectionParam) {
            /** @var $reflectionParam \ReflectionParameter  */
            $constructParamClass = $reflectionParams->getClass()->getName();
            $constructParamName = $reflectionParam->getName();
            $serviceObject = $this->_dic->get($constructParamName, $constructParamClass);
            $constructParams[] = $serviceObject;
        }
        return $constructParams;
    }        
}