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
    
  
    /**
     * By default, insert new (by create) Item. 
     * 
     * It can't overwrite existing item by default. 
     * You can get item "id" for creatad item us result this function.
     * 
     * If  $item["id"] !== null, item set with that id. 
     * If item with same id already exist - method will throw exception, 
     * but if $rewriteIfExist = true item will be rewrited.<br>
     * 
     * If $item["id"] is not set or $item["id"]===null, 
     * item will be insert with autoincrement PrimryKey.<br>
     * 
     * @param array $itemData associated array with or without PrimaryKey
     * @return int|string|null  "id" for creatad item
     */
    public function create($itemData, $rewriteIfExist = false) {
        $identifier = $this->getIdentifier();
        $db = $this->_dbTable->getAdapter();
        $db->beginTransaction();
        try {
            if (isset($itemData[$identifier]) && $rewriteIfExist) {
                $where = $db->quoteInto( $identifier . ' = ?', $itemData[$identifier]);  
                $errorMsg = 'Cann\'t delete item with "id" = ' . $itemData[$identifier];
                $this->_dbTable->delete($where);
            }
            $errorMsg = 'Cann\'t insert item';
            $id = $this->_dbTable->insert($itemData);
            $db->commit();
        }
        catch (Zend_Db_Exception $e) {
            $db->rollback();
            require_once 'Zaboy/DataStores/Exception.php';
            throw new Zaboy_DataStores_Exception( $errorMsg, 0, $e );
        } 
        return $id;
    }

    /**
     * By default, update existing Item with PrimaryKey = $item["id"].
     * 
     * If item with PrimaryKey == $item["id"] is existing in store, item will updete.
     * Filds wich don't present in $item will not change in item in store.
     * Method will return 1<br>
     * 
     * If $item["id"] isn't set - method will throw exception.
     * If item with PrimaryKey == $item["id"] is absent - method do nothing and return 0,
     * but if $createIfAbsent = true item will be created and method return 1.<br>
     * 
     * 
     * @param array $itemData associated array with PrimaryKey
     * @return int number of updeted (created) items: 0 or 1
     */
    public function update($itemData, $createIfAbsent = false) {
        $identifier = $this->getIdentifier();
        if (!isset($itemData[$identifier])) {
            require_once 'Zaboy/DataStores/Exception.php';
            throw new Zaboy_DataStores_Exception('Item must has primary key'); 
        }
        $id = $itemData[$identifier];
        $this->_checkIdentifierType($id);       
        if ( $createIfAbsent ){
            $row = $this->_dbTable->createRow($itemData);
            $row->save();
            $updatedItemsCount = 1;
        }else{
            $where = $this->_dbTable->getAdapter()->quoteInto($identifier . ' = ?', $id);
            $updatedItemsCount = $this->_dbTable->update($itemData, $where);
        }
        return $updatedItemsCount;
    }

     /**
      * Delete Item by id. Method do nothing if item with that id is absent.
      * 
      * @param int|string $id PrimaryKey
      * @return int number of deleted items: 0 or 1
      */
    public function delete($id) {
        $identifier = $this->getIdentifier();
        $this->_checkIdentifierType($id);       
        $where = $this->_dbTable->getAdapter()->quoteInto($identifier . ' = ?', $id);
        $deletedItemsCount = $this->_dbTable->delete( $where);
        return $deletedItemsCount;
    }  
}