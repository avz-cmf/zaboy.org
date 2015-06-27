<?php
/**
 * Zaboy_DataStore_ArrayObject
 * 
 * @category   DataStore
 * @package    DataStore
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/DataStores/Abstract.php';

/**
 * class Zaboy_DataStore_ArrayObject
 * 
 * @category   DataStore
 * @package    DataStore
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 */
class Zaboy_DataStore_ArrayObject extends Zaboy_DataStores_Abstract
{    
    /**
     * Collected items
     * @var array
     */
    protected $_items = array();
    
    /**
     * 
     * @param array $options
     * @param Traversable $itemsSorce
     */
    public function __construct( $options, Traversable $itemsSorce)
    {
        parent::__construct($options);
        $identifier = $this->getIdentifier();
        foreach ($itemsSorce as $item) {
            if ( isset($item[$identifier]) ) {
                $id = $item[$identifier];   
                $this->_checkIdentifierType($id);
                unset($item[$identifier]);
                $this->_items[$id] = array_merge(array($identifier => $id), $item);
            }
        }
    }           
            
            
    /**
     * Return Item by id
     * 
     * Method return null if item with that id is absent.
     * Format of Item - Array("id"=>123, "fild1"=value1, ...)
     * 
     * @param int|string|float $id PrimaryKey
     * @return array|null
     */
    public function read($id)
    {
        $this->_checkIdentifierType($id);
        if (isset($this->_items[$id]) ) {
           return $this->_items[$id]; 
        }else{
            return null;
        }
        
    }
    
    /**
     * Return items by criteria with mapping, sorting and paging
     * 
     * Example:
     * <code>
     * find(
     *    array('fild2' => 2, 'fild5' => 'something'), // 'fild2' === 2 && 'fild5 === 'something' 
     *    array(self::DEF_ID), // return only identifiers
     *    array(self::DEF_ID => self::DESC),  // Sorting in reverse order by 'id" fild
     *    10, // not more then 10 items
     *    5 // from 6th items in result set (offset of the first item is 0)
     * ) 
     * </code>
     * 
     * ORDER
     * http://www.simplecoding.org/sortirovka-v-mysql-neskolko-redko-ispolzuemyx-vozmozhnostej.html
     * http://ru.php.net/manual/ru/function.usort.php
     * 
     * @see ASC
     * @see DESC
     * @param string|int|Array|null $where   
     * @param array|null $filds What filds will be included in result set. All by default 
     * @param array|null $order
     * @param int|null $limit
     * @param int|null $offset
     * @return array    Empty array or array of arrays
     */
    public function find(
        $where = null,             
        $filds = null, 
        $order = null,            
        $limit = null, 
        $offset = null 
    ) {
        $resultArray = array();
        /*********************** $where ***********************
         * 
        
        
        
        $whereCheckArray = array();
        foreach ($where as $fild => $value) {
            if('null'=== strtolower($value )){
                $whereCheckArray[] = "!isset(\$item['$fild'])";
            }else{
                $whereCheckArray[] = 
                        "isset(\$item['$fild']) && "
                      . "\$item['$fild'] == '$value'"
                ;
            }
        }
        $whereCheckCondition = "(" . implode(') && (', $whereCheckArray) . ")";
        $whereCheckString = 
                "if ( $whereCheckCondition ) {" 
                    . '$ArrayObject->append($key, $item);' 
                . '}';
        $whereCheckFunction = create_function('$item, $key, $arrayObject', $whereCheckString);
        $arrayObject = new ArrayObject(array());
        array_walk($this->_items, $whereCheckFunction, $arrayObject);
        // *********************** ^^^$where^^^ ***********************

        */
        if ( isset($where) ) {
        $whereBody = "";
            foreach ($where as $fild => $value) {
                if('null'=== strtolower($value )){
                    $whereCheck = "!isset(\$item['$fild'])";
                }elseif('not_null'=== strtolower($value )){
                    $whereCheck = "isset(\$item['$fild'])";
                }else{
                    $whereCheck = 
                        "isset(\$item['$fild']) && "
                        . "\$item['$fild'] == '$value'"
                    ;                
                }
                $whereBody = 
                     $whereBody . '&& ' 
                    . '( ' .  $whereCheck . ' )' . PHP_EOL
                ;
            }
            
            var_dump($whereBody);   
            $whereFunctionBody = PHP_EOL  .
                '$result = ' . PHP_EOL 
                . substr($whereBody, 2) . ';' . PHP_EOL 
                . 'return $result;'
            ;
            
            var_dump($whereFunctionBody);            
            
            $whereFunction = create_function('$item', $whereFunctionBody);

            foreach ($this->_items as $item) {
                if($whereFunction($item)) {
                    $resultArray[] = $item;
                }
            }
        }else{
            $resultArray = $this->_items;
        }
        
        // ***********************   order   ***********************        
        if (!empty($order)) {
            $nextCompareLevel ='';
            foreach ($order as $ordKey => $ordVal) {
                if($ordVal === self::ASC){
                    $cond = '>'; $notCond = '<';
                }elseIf($ordVal === self::DESC){
                    $cond = '<'; $notCond = '>';
                }else{
                    require_once 'Zaboy/DataStores/Exception.php';
                    throw new Zaboy_DataStores_Exception('Invalid condition: ' . $ordVal);    
                }    
                $prevCompareLevel = 
                    "if (\$a['$ordKey'] $cond \$b['$ordKey']) {return 1;};" . PHP_EOL 
                    . "if (\$a['$ordKey'] $notCond  \$b['$ordKey']) {return -1;};" . PHP_EOL
                ;
                $nextCompareLevel =$nextCompareLevel . $prevCompareLevel;                 
            }
            $sortFunctionBody = $nextCompareLevel . 'return 0;';
            $sortFunction = create_function('$a,$b', $sortFunctionBody);
            usort($resultArray, $sortFunction);
        }
        // *********************  ^^ order ^^   ***********************  
        return $resultArray;
    } 

}