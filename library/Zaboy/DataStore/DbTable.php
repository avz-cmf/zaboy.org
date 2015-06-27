<?php
/**
 * Zaboy_DataStore_DbTable
 * 
 * @category   DataStore
 * @package    DataStore
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/DataStores/Abstract.php';

/**
 * class Zaboy_DataStore_DbTable
 * 
 * @category   DataStore
 * @package    DataStore
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 */
class Zaboy_DataStore_DbTable extends Zaboy_DataStores_Abstract
{    
    /**
     *
     * @var Zend_Db_Table 
     */
    protected $_dbTable;
    
    public function __construct( $options, Zend_Db_Table $dbTable)
    {
        parent::__construct($options);
        $this->_dbTable = $dbTable;
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
        $identifier = $this->getIdentifier();
        $row = $this->_dbTable->fetchRow($this->_dbTable->select()->where($identifier . ' = ?', $id));
        if (isset($row) ) {
           return $row->toArray(); 
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
        $select = $this->_dbTable->select();
        
        // ***********************   where   *********************** 
        if (!empty($where)) {
            foreach ($where as $fild => $value) {
                $select->where( $fild . '= ?', $value );
            }
        }    
        
        // ***********************   order   *********************** 
        $orderForSQL = array();
        if (!empty($order)) {
            foreach ($order as $ordKey => $ordVal) {
                $orderForSQL[] = $ordKey . ' ' . $ordVal;
            }
        }else{
            $orderForSQL[] = $this->getIdentifier();
        }
        $select->order($orderForSQL);
        
        // *********************  limit, offset   *********************** 
        $select->limit($limit, $offset);
        
        // *********************  filds  *********************** 
        if (!empty($filds)) {
            $select->from($this->_dbTable, $filds);
        }            

        // ***********************   return   *********************** 
        $rows = $this->_dbTable->fetchAll($select);
        return $rows->toArray();
    }    
}