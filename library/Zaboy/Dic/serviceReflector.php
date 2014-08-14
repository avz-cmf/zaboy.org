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
     * array array( int => value)
     */
    private $_constructParams = array();
    
    /**
     * 
     * param string class name for extract information about _construct()'s parameters
     * @return void
     */  
    public function __construct( $serviceName, $serviceClass, Zaboy_Dic_ServicesConfigs $servicesConfigs ) 
    {
        $this->_serviceName = $serviceName;        
        $this->_serviceClass = $serviceClass;
        $this->_servicesConfigs = $servicesConfigs;
        
        $reflectionObject = new ReflectionClass($serviceClass);
        //Get params for $className::__construct
        $reflectionConstruct = $reflectionObject->getMethod('__construct');
        $reflectionParams = $reflectionConstruct->getParameters();
        //extract option (if exist) from array $reflectionParams
        $reflectionParams = $this->_resolveOptions($reflectionParams);
        foreach ($reflectionParams as $constructParam) {
            /** @var $constructParam \ReflectionParameter  */
            $this->_resolveServiceClass($constructParam);
            
            
            $constructParamInfo = $this->_getParamInfoFromReflection($callParam); 
            //$constructParamInfo - array 'paramPosition','paramName', 'paramClass', 'isOptional'            
$isCallParamService = is_a( $callParamInfo['paramClass'], 'Avz_Dic_Service_Interface' , true ); //is it Service?
$isCallParamServiceInConfig = key_exists( $callParamInfo['paramName'] , $this->_servicesConfig ); 
$isCallParamOptional = $callParamInfo['isOptional'];
if ( $isCallParamService && ( !$isCallParamOptional || $isCallParamServiceInConfig) ) {//is it service not optional or present in config?
    $value = $this->get($callParamInfo['paramName'], $callParamInfo['paramClass']);
    return $value;
} 

//add search info about param in: $_egisterdParams (from config.ini), Resurses, ZendRegistry
if ($callParamInfo['isOptional']) { return null;}

require_once 'Avz/Exception.php';
throw new Avz_Exception('Param ' . $callParamInfo['paramName'] . ' in ' .  $serviceName . ':: _consruct() is not resolved'); 

        }
        //all prams are ready in $callParamsArray
         return $callParamsArray;
    }       

    /**
     * @return array
     */
    public function getConstructParams()
    { 
        return $this->_constructParams;
    }    
    
    /**
     * Make decision about class for Service
     * 
     * information about class from config is more important
     * 
     * @param string $serviceName
     * @param string $serviceClassFromParam
     * @return string
     */
    public function _resolveServiceClass($constructParam) 
    {
        $constructParamName = $constructParam->getName();
        $serviceClassFromConfig = $this->_servicesConfigs->getServiceClass($constructParamName);
         if (isset ($serviceClassFromConfig)) {
            //information about class from config is more important
            $serviceClass =  $serviceClassFromConfig;
         }else{
            if ($constructParam->getClass()!== null) {
                $serviceClass = $constructParam->getClass()->getName();
            }else{
                require_once 'Zaboy/Dic/Exception.php';
                throw new Zaboy_Dic_Exception("Service Class isn't defined for ($constructParamName)"); 
            }
        }
        $this->_constructParams[$constructParamName] = $serviceClass;
    }
    
    /**
     * @param string $serviceName
     * @param array $callParamInfo 
     */
    protected function _resolveServiceOptions($reflectionParams) 
    {
        if (isset($reflectionParams[0])) {
            $mayBeOptionsParam = $reflectionParams[0];
            /** @var $mayBeOptionsParam \ReflectionParameter  */
            $mayBeOptionsParamName = $mayBeOptionsParam->getName();
            if ($mayBeOptionsParamName === 'options') {
                $optionsFromConfig = $this->_servicesConfigs->getServiceOptions($this->_serviceName);
                $this->_constructParams[] = $ptionsFromConfig;
                array_shift($reflectionParams);               
            }           
        }
        return $reflectionParams;
     }        

}