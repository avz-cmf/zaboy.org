<?php
/**
 * Avz_DataSources_Interface
 * 
 * @category   DataSources
 * @package    DataSources
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Avz/DataSources/Interface.php';
require_once 'Avz/Abstract.php';

/**
 * Avz_DataSources_Interface
 * 
 * <b>DataSources</b> is abstract storage for <i>Items</i>.<br>
 * You can think about this storage as it is table or array with <i>Items</i>.<br>
 * <i>DataSources</i> must has Primary Key - see {@link getIdentifier()}.<br>
 * <b>Items</b> is array:
 * In simple case:
 * <code>
 *  array(
 *  'id' => 'sameObjectName', 'objectInstance' => new objectTipe()
 *  )
 * </code> 
 * if <i>Items</i> is record:
 * <code>
 *  array(
 *  'id' => 'item1', 'fild_A' => 'valut_1A', 'fild_B' =>  'valut_1B' ...
 *  )
 * </code>
 * 
 * @todo  public function fetch( $where = null, $options=null ); from Avz_DataStore_Read_Interface
 * @category   DataSources
 * @package    DataSources
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
interface Avz_DataSources_Interface extends IteratorAggregate
{

    /**
     * Default identifier
     * 
     * @see getIdentifier()
     */
    const DEFAULT_IDENTIFIER = 'id';
    
    

    /**
     * Create new record in data sourcer
     * 
     * If Item with same id already exist in DataSource - exception will be thrown
     * 
     * @param array $item - see about <i>Item</i> in {@link Avz_DataSources_Interface}
     * @return mix it's "id" - Primary Key index for new record which was created or last inserted
     */    
    public function createItem($item);
    

    /**
     * Read record from data sourcer
     * 
     * If Item with  PrimaryKey = $id is absent in DataSource - return null
     * 
     * @param mix $id
     * @return array|null
     */    
    public function retriveItem($id);

    /**
     * Update Item
     * 
     * If $item['id'] is not set - exception will be thrown. Use createItem().<br>
     * If Item with id = $item['id'] is absent in DataSource - exception will be thrown. Use createItem().<br>
     * If DataSource has Item with id = $item['id'] and it has more elements than $item - these elements will not modified.<br>
     * 
     * @param array $item
     * @return void
     */   
    public function updateItem($item);
    
    /**
     * Delete Item by PrimaryKey
     * 
     * If Item with this id already absent - method do nothing
     * 
     * @param mix $id
     * @return void
     */
    public function deleteItem($id);
    
    
    /**
     * Return list of keys
     * 
     * @return array
     */
    public function getKeys();
    
    /**
     * return name of Primary Key - by default {@link DEFAULT_IDENTIFIER}
     * 
     * return string name of Primary Key
     */
    public function getIdentifier();    

}