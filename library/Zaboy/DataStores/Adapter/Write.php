<?php
/**
 * Avz_DataStores_Abstract
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

//require_once 'Avz/DataStore/Interface.php';
require_once 'Avz/Abstract.php';

/**
 * Avz_DataStores_Abstract
 * 
 * <b>DataStore</b><br>
 * <i>DataStore</i> ready work with data just after creating.<br>
 *  <i>Metadata</i>is changeable after setting , but it is not recommended.<br>
 * <b>DataStore</b> is included:
 * <ul>
 * <li><b>DataSource</b> - object with data (it may be table, REST service, ect ...). 
 *        It must has primary key. See {@link Avz_DataSources_Abstract}</li>
 * 
 * <li><b>Metadata</b> - It is important info about DataStore  (behavior rules, character encoding ...).
 *        You can set <i>Metadata</i> in $options via {@link __construct()}
 * <code>
 *  new Avz_DataStore( array Avz_DataStore_Abstract::KEY_METADATA => array('key' => 'val') )
 * </code>
 * or in application.ini
 * <code>
 *  resources.dic.dsDataStoreDbTable.dbtDbTableForSource.class = Awz_Db_Table
 *  resources.dic.dsDataStoreDbTable.dbtDbTableForSource.options.name = "my-table"
 *  resources.dic.service.dsoSource.class = 'Avz_DataSource_DbTable'
 *  resources.dic.service.dstStoreDbTable.class = 'Avz_DataStore_DbTable'
 *  resources.dic.service.dstStoreDbTable.options.metadata.key = val
 * </code>
 * and after it - get object of Store from Dic
 * <code>
 *  global $application;
 *  $bootstrap = $application->getBootstrap();
 *  $dic = $bootstrap->getResource('dic');
 *  $dsDataStoreDbTable = $dic->get('dstStoreDbTable');   
 * </code>
 *
 * <li> <b>All rest</b>  will be in attribs. See {@link getAttrib()}</li>
 * </ul>
 * 
 * @see Avz_DataStore_Interface
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @todo Move identifier and label to $_metadata
 */
abstract class Avz_DataStores_Abstract extends Avz_Abstract implements Countable, IteratorAggregate
{
    /**
     * @see _metadata
     */
    const KEY_METADATA = 'metadata';   

    /**
     * Data container metadata
     * @var array
     */
    protected $_metadata = array();   

    /**
     * Data container 
     * @var Avz_DataSources_Abstract 
     */
    protected $_dataSource;
    
    /**
     * @param array $options
     * @param Avz_DataSources_Abstract $dataSource
     */
    public function __construct($options, Avz_DataSources_Interface $dsoSourc) 
    {
        parent::__construct($options);
        $this->_dataSource = $dsoSourc;
    }   
 
    /**
     * Set metadata by key => value, if $key = null set all at once as array
     *
     * @param  array $metadata
     * @return Avz_DataStore_Abstract
     */
    public function setMetadata($metadata, $key = null)
    {
         if ( isset($key) ) {
             $this->_metadata[$key] = $metadata;
         }elseif ( is_array($metadata) ) {
            foreach ($metadata as $key => $value) {
                $this->_metadata[$key] = $value;
            }
        }else{
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception('Invalid $metadata; please use array');
        }
        return $this;   
    }

    /**
     * Get metadata item or all metadata
     *
     * @param  null|string $key Metadata key when pulling single metadata item
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        if (null === $key) {
            return $this->_metadata;
        }elseif (array_key_exists($key, $this->_metadata)) {
            return $this->_metadata[$key];
        }else{
            return null;
        }
    }

    /**
     * Clear individual or all metadata item(s)
     *
     * @param  null|string $key
     * @return Avz_DataStore_Abstract
     */
    public function removeMetadata($key = null)
    {
        if (null === $key) {
            $this->_metadata = array();
        } elseif (array_key_exists($key, $this->_metadata)) {
            unset($this->_metadata[$key]);
        }
        return $this;        
    }


    /**
     * Does an item with the given identifier exist?
     *
     * @param  string|int $id
     * @return bool
     */
    public function hasItem($id)
    {
        try {
            $item = $this->_dataSource->retriveItem($id);
        } catch (Exception $exc) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception(
                    __METHOD__ . '($id) throws Exception. $id = ' . $id,
                    0,
                    $exc
            );
        }
        $result = isset($item);
        return $result;           
    }        
    
    /**
     * Retrieve an item by identifier
     *
     * Item retrieved will be flattened to an array.<br>
     * <code>
     *     array("id" = 3, "fild1" = ...);
     * </code>
     * If Item absent - method return NULL<br>
     * 
     * @see Avz_DataSources_Abstract::retriveItem()
     * @param  string $id
     * @return array
     */
    public function getItem($id)
    {
        try {
            $item = $this->_dataSource->retriveItem($id);
        } catch (Exception $exc) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception(
                    __METHOD__ . '($id) throws Exception. $id = ' . $id,
                    0,
                    $exc
            );
        }
        return $item;           
    }              
            
    /**
     * Set (add new or override existed) an individual item, optionally by identifier
     * 
     * If  $item['id'] === nul - $id will define by autoincrement. Method return Last Inserted Id <br>
     * If $id is defined and item with same Primry key exist -->> Item will rewrite.     <br>
     * In last case, method of rewriting is dependent of specific of data source. In most case it will be "delete" + "create".<br>
     * 
     * 
     * @param  array $item even if you write simple type - use array('fildName'=>5) or array('id' => 10, 'fildName'=>5)
     * @return mix  int it's "id" - Primary Key index for new record which was created or last inserted
     */
    public function setItem($item)
    {
        $normalItem = $this->_normalizeItem($item);
        $identifier = $this->getIdentifier();
        if (isset($normalItem[$identifier])) {
            $id = $normalItem[$identifier];
            $isSameItemExist = $this->hasItem($id);
            if ( $isSameItemExist ) {
                $this->removeItem($id);
            }    
        }         
        try {
            $insertedId = $this->_dataSource->createItem($normalItem);
        } catch (Exception $exc) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception(
                    __METHOD__ . '($item) throws Exception. $item = ' . print_r($normalItem),
                    0,
                    $exc
            );
        }
        return $insertedId;
    }
    

    /**
     * Add (by create new item - not override/update) an individual item, optionally by identifier
     * 
     * If  $item['id'] === nul - $id will define by autoincrement. Method return Last Inserted Id <br> <br>
     * If $id is defined and item with same Primry key exist -->> will throws exception. ( use setItem() )     <br>
     *
     * @param  array|object $item
     * @return array inserted Item
     */
    public function addItem($item)
    {       
        $normalItem = $this->_normalizeItem($item);
        $identifier = $this->getIdentifier();
        if (isset($normalItem[$identifier])) {
            $id = $normalItem[$identifier];
            $isSameItemExist = $this->hasItem($id);
            if ( $isSameItemExist ) {
                require_once 'Avz/DataStores/Exception.php';
                throw new Avz_DataStores_Exception('Overwriting items using addItem() is not allowed. Use setItem().');
            }    
        }         
        try {
            $insertedId = $this->_dataSource->createItem($normalItem);
        } catch (Exception $exc) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception(
                    __METHOD__ . '($item) throws Exception. $item = ' . print_r($normalItem),
                    0,
                    $exc
            );
        }
        return $insertedId;
    }
    
   /**
     * Set the items from array or from Traversable object
     *
    * All contaned data will be remove before set
    * 
     * @param array|Traversable $items
     * @return count of items which was inserted
     */
    public function setItems($items)
    {
        if (!is_array($items) && (!is_object($items) || !($items instanceof Traversable))) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception('Only null, arrays and Traversable objects may be added to ' . __CLASS__);
        }
        $this->removeItems();   
        $itemsCount = 0;
        foreach ($items as $item) {
            $this->addItem($item);
            $itemsCount = $itemsCount +1;
        }
        return $itemsCount;      
    }  
    
   /**
     * Get all items at once
     *
     * @return array
     */
    public function getItems()
    {
        $result = array();
        foreach ($this as $id => $item) {
            $result[$id] = $item;
        }        
        return $result;      
    }  
    
    /**
     * Remove item by identifier
     * 
     * If Iten is not exist - method do nothing
     *
     * @see Avz_DataSources_Interface
     * @param  string $id
     * @return void
     */
    public function removeItem($id)
    {        
        try {
            $result = $this->_dataSource->deleteItem($id);                
        } catch (Exception $exc) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception(
                    __CLASS__ . '::deleteItem() throws Exception. $id=' . $id,
                    0,
                    $exc
            );
        }
        return $result;     
    }
    
    /**
     * Remove all items
     * 
     * If Items are not exist - method do nothing
     *
     * @return void
     */
    public function removeItems()
    {
        foreach ($this as $itemId => $item) {
            $this->removeItem($itemId); 
        }
        return;   
    }        

    /**
     * @see Avz_DataSources_Interface::getIdentifier()
     * @return void
     */
    public function getIdentifier()
    {    
        return $this->_dataSource->getIdentifier();
    }
       
//-----------------------------------------------------------------------------
    /**
     * Check getIdentifier() and $item type 
     *
     * @param  array|object $item
     * @return array
     */
    protected function _normalizeItem($item)
    {
        $identifier = $this->getIdentifier();
        if (null === $identifier) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception('You must set an identifier prior to adding items');
        }
        if (!is_object($item) && !is_array($item)) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception('Only arrays and objects may be attached');
        }
        if (is_object($item)) {
            if (method_exists($item, 'toArray')) {
                $item = $item->toArray();
            } else {
                $item = get_object_vars($item);
            }
        }
        return $item;
    }
      
//************************** IteratorAggregate ************************
    /**
    * @see IteratorAggregate 
    * @return IteratorAggregate
     */
    public function  getIterator()
    {
        return $this->_dataSource->getIterator();
    }   
    
//********************** coutable *******************************    
    /**
    * @see coutable
    * @return int
     */
    public function count()
    {
        try {
            $keys = $this->_dataSource->getKeys();
        } catch (Exception $exc) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception(
                    __METHOD__ . ' throws Exception.',
                    0,
                    $exc
            );
        }
        return count($keys);
    }        
}