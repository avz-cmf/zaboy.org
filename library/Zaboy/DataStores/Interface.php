<?php
/**
 * Avz_DataStore_Read_Interface
 * 
 * @category   DataStore
 * @package    DataStore
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Avz_DataStore_Read_Interface
 * 
 * @see Avz_DataStore_Interface
 * @category   DataStore
 * @package    DataStore
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
interface Avz_DataStore_Interface extends ArrayAccess, Iterator, Countable
{
//**************  Avz_DataStore_Interface *************************  
   /**
    * Param $where can be null, numeric, string or array<br>
    * <br>
    * <b>null, numeric, string</b><br>
    * If param $where is not array - it is for "Primary Key" ( See {@link getIdentifier()} )
    * <code>
    * fetch (), fetch (null) -->> select 
    * fetch("5") -->> where 'id'='5'
    * fetch(5) -->> where 'id'=5
    * fetch('>5') -->> where <b>'id' = '>5'</b> //but what do you think? :-) Use $where = array( 'id'=>...
    * </code>
    * <b>array</b><br>
    * If param $where is array, it may be:
    * <ul>
    * <li>associative array 'keyName'=>'condition' without numeric indexes</li>
    * <li>simple array of associative arrays</li>
    * <br> 
    * </ul> 
    * <b>combine conditions via AND</b><br>
    * If param $where is associative array - it means "keyName" => "condition"
    * <code>
    * fetch(array("id" => "10", "fild" => "> 5") -->> where 'id'=10 AND "fild">5
    * fetch (array("id" => "<>666", "fild" => "<> '666'") -->> where 'id'="<>666" AND "fild"<>6666 //sic!
    * fetch (array("fild" => 'NOT NULL') -->> where 'fild' IS NOT NULL
    * fetch (array("fild" => 'NULL') -->> where 'fild' IS NULL
    * </code>
    * <b>combine conditions via OR</b><br>
    * If param $where is simple array of arrays (subarray) - it means   subarray1 OR subarray2 OR ...
    * <code>
    * fetch(
    *     array(
    *         array("id" => " > 0", "fild" => " = 'ok'")
    *         array("id" => " < 10")
    *     )
    * )
    * -->> where "id" > 0 AND "fild" = 'ok' OR "id" < 10
    * </code>
    * <b>Note</b><br>
    * <code>
    * array('id' => '10') === array('id' => ' = 10')
    * array('id' => '<> NULL') -->> WHERE 'id' <> 'NULL' // you have to use array('id' => 'NOT NULL'), if you want WHERE 'id' NOT NULL
    * </code>
    * <br>
    * <b>Param $options</b><br>
    * Can has 3 keys
    * <ul>
    * <li>ORDER</li>
    * <li>LIMIT</li>
    * <li>OFFSET</li>
    * <br> 
    * <code>
    * array(
    * 'ORDER' => '+id',
    * 'LIMIT' => 10 // if nuul - it s nolimit
    * 'OFFSET' => 0 // if 'OFFSET' => 10 start will be from 11 ( if count from 1)
    * )
    * </code>
    * 
    * @param string|array {@link Avz_DataStore_Interface::fetch()}
    * @param array it may contains ORDER LIMIT ect.
    * @return array|object of array's ArrayAccess,Traversable,Countable
    */
    public function fetch( $where = null, $options=null );
    
     /**
     * Set identifier for item lookups
     *
     * @param  string|int|null $identifier
     * @return Zend_Dojo_Data
     */
    public function setIdentifier($identifier);
            
    /**
     * Retrieve current item identifier
     *
     * @return string|int|null
     */
    public function getIdentifier();

    /**
     * Does an item with the given identifier exist?
     *
     * @param  string|int $id
     * @return bool
     */
    public function hasItem($id);    
    
    /**
     * Retrieve an item by identifier
     *
     * Item retrieved will be flattened to an array.
     *
     * @param  string $id
     * @return array
     */
    public function getItem($id);
    /**
     * Add an individual item, optionally by identifier
     *
     * @param  array|object $item
     * @param  string|null $id
     * @return Zend_Dojo_Data
     */
    public function addItem($item, $id = null);
            
    /**
     * Set an individual item, optionally by identifier (overwrites)
     *
     * @param  array|object $item
     * @param  string|null $identifier
     * @return Zend_Dojo_Data
     */
    public function setItem($item, $id = null);
    
 
    /**
     * Remove item by identifier
     *
     * @param  string $id
     * @return Zend_Dojo_Data
     */
    public function removeItem($id);    
    
     /**
     * Get all items as an array
     *
     * Serializes items to arrays.
     *
     * @return array
     */
    public function getItems(); 

   /**
     * Set the items to collect
     *
     * @param array|Traversable $items
     * @return Zend_Dojo_Data
     */
    public function setItems($items);    
    
    /**
     * Remove all items at once
     *
     * @return Zend_Dojo_Data
     */
    public function removeItems();    

    /**
     * Set label to use for displaying item associations
     *
     * @param  string|null $label
     * @return Zend_Dojo_Data
     */
    public function setLabel($label);

    /**
     * Retrieve item association label
     *
     * @return string|null
     */
    public function getLabel();
    
    /**
     * Set metadata by key => value
     *
     * @param  array $metadata
     * @return Avz_DataStore_Abstract
     */
    public function setMetadata($metadata);

    /**
     * Get metadata item or all metadata
     *
     * @param  null|string $key Metadata key when pulling single metadata item
     * @return mixed
     */
    public function getMetadata($key = null);

    /**
     * Clear individual or all metadata item(s)
     *
     * @param  null|string $key
     * @return Zend_Dojo_Data
     */
    public function removeMetadata($key = null);

    /**
     * Load object from array
     *
     * @param  array $data
     * @return Zend_Dojo_Data
     */
    public function fromArray(array $data);
    
    /**
     * Seralize entire data structure, including identifier and label, to array
     *
     * @return array
     */
    public function toArray();    
    
     /**
     * Load object from JSON
     *
     * @param  string $json
     * @return Zend_Dojo_Data
     */
    public function fromJson($json);   
    
    /**
     * Serialize to JSON (dojo.data format)
     *
     * @return string
     */
    public function toJson();
    
     
    /**
     * Seralize entire data to array. Primary key - 'id' (for Dojo)
     * 
     * out <br>
     * </code>
     *      array(
     *          Avz_DataStore_Array::KEY_IDENTIFIER => 'anotherId',
     *          Avz_DataStore_Array::KEY_LABEL => 'it is label',            
     *          Avz_DataStore_Array::KEY_METADATA  => array('keyMetadata1' => 'valMetadata1'),     
     *          Avz_DataStore_Array::KEY_ITEMS  => 
     *              array(
     *                  array( 'anotherId' => 1, 'fString' => 'val1', 'fInt' => 10),
     *                  array( 'anotherId' => 2, 'fString' => 'val2', 'fInt' => 20),
     *                  array( 'anotherId' => 3, 'fString' => 'val3', 'fInt' => 30)                    
     *              )             
     *      )
     * </code>
     * out <br>
     * </code>
     *              array(
     *                  array( 'id' => 1, 'fString' => 'val1', 'fInt' => 10),
     *                  array( 'id' => 2, 'fString' => 'val2', 'fInt' => 20),
     *                  array( 'id' => 3, 'fString' => 'val3', 'fInt' => 30)                    
     *              ) 
     * </code>
     * 
     * </code>
     * @return array
     */
    public function itemsToArray();
    
    /**
     * @return Json for dojo data object
     */
    public function itemsToJson();    
   
//************************** ArrayAccess ************************    
 
    
//************************** Iterator ************************


//********************** coutable *******************************    
 
}