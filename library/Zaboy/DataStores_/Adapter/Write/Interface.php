<?php
/**
 * Zaboy_Dic_DataStores_Adapter_Write_Interface
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Interface Zaboy_Dic_DataStores_Adapter_Write_Interface
 * 
 * 
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 * @see http://rudocs.exdat.com/docs/index-567188.html?page=5
 */
interface Zaboy_Dic_DataStores_Adapter_Write_Interface
{    
    const CRUD_METADATA = 'crud-metadata';
        const METADATA_KEY_IDENTIFIER = 'identifier';
        
    const CRUD_LAST_INSERTED_ID = 'crud-last-inserted-id';

    /**
     * Default identifier
     * 
     * @see getIdentifier()
     */
    const DEFAULT_IDENTIFIER = 'id';
    

    /**
     * Insert new (by create) Item. It can't overwrite existing item. You can get item "id" by call getLastInsertedId()
     * 
     * If  $item[getPrimaryKey()] !== null, item set with that id. But if item with same id already exist in store - method will throw exception
     * 
     * If $item[getPrimaryKey()] is not set or ===null, item will be insert with autoincrement PrimryKey.<br>
     * You can get item's "id" by call getLastInsertedId() in this case<br>
     * 
     * @param array $item associated array with or without PrimaryKey
     * @return Avz_DataStore_Abstract
     */
    public function create($itemData, $rewriteIfExist = false);
    
     /**
     * Return Item by id. Method return null if item with that id is absent.
     * 
     * @param int|string $id PrimaryKey
     * @return array
     */
    public function read($id =null);
    
    public function crudFind(
        $filds = array(self::DEFAULT_IDENTIFIER), 
        $equalities = array(), 
        $order = array(),            
        $limit = 0, 
        $offset = 0 
    ); 
    
    //getKeys
    //Count
    //
    
     
     /**
     * Update existing Item with PrimaryKey = $item[getPrimaryKey()]. You can't get that Id by call getLastInsertedId()
     * 
     * If item with this PrimryKey is existing in the store, this item will be updated<br>
     * Filds wich don't present in $item will not change in stores item<br>
     * If item with this PrimryKey is absent in the store, method throws exception<br>
     * You can not get item's id by call getLastInsertedId()<br>
     * 
     * @param array $item associated array with or without PrimaryKey
     * @return Avz_DataStore_Abstract
     */
    abstract public function update($itemData, $createIfAbsent = false);
    
     /**
     * Delete Item by id. Method do nothing if item with that id is absent.
     * 
     * @param int|string $id PrimaryKey
     * @return Avz_DataStore_Abstract
     */
    abstract public function delete($id = null); 


    
}