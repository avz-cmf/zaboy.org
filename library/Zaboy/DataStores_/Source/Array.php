<?php
/**
 * Avz_DataStore_Array
 * 
 * @category   DataStore
 * @package    DataStore
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Avz/DataStore/Abstract.php';

/**
 * Avz_DataStore_Array
 * 
 * @see Avz_DataStore_Interface
 * @category   DataStore
 * @package    DataStore
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Avz_DataStore_Array extends Avz_DataStore_Abstract
{  
    const KEY_ITEM = 'item';
    
    /**
     * Identifier field of item
     * @var string|int
     */
    protected $_identifier = self::DEFAULT_IDENTIFIER; // = 'id'

    /**
     * Collected items
     * @var array
     */
    protected $_items = null;

    /**
     * Label field of item
     * @var string
     */
    protected $_label;

    /**
     * Data container metadata
     * @var array
     */
    protected $_metadata = array();
    
     /**
     * @see _prepareEvalExprForSort()
     * @var string
     */
    protected $_evalExprForSort;
            
//**************  Avz_DataStore_Interface *************************  
   /**
    * fetch( $where, $options=null ) for search , order,affset and limit in DataStore
    * 
    * <br>
    * <b> Param $where  can string only</b> <br><br>
    * A column name must be enclosed in quotation ``
    * <code> 
    * `field1` = 44 
    * </code>
    * String  must be enclosed in quotation '' (not " ")
    * <code> 
    * `field1` = 'value'
    * </code>
    * You can combine conditions
    * <code> 
    * `field1` = 'value' AND `field1` = 44 
    * </code>
    * You can use AND , OR , = , != , >= , <= , ( ) , !( ) , + - * / , IS NULL , IS NOT NULL
    * <code> 
    * `field1` = 'value' AND ( `field2` IS NULL OR `field2` != ( `field1` * 2 ) )  
    * </code>
    * <br>
    * <b>Param $options</b> is array which can has 4 keys<br><br>
    * <ul>
    * <li>ORDER</li>
    * <li>LIMIT</li>
    * <li>OFFSET</li>
    * <li>FIELDS</li>
    * </ul>
    * <br> 
    * <code>
    * array(
    *     'FIELDS' => array('id', 'field1', 'field2'),//define count and order of columns in result
    *     'ORDER' => '+id, -field1',
    *     'LIMIT' => 10 ,// if nuul - it is nolimit
    *     'OFFSET' => 5 // if 'OFFSET' => 10 start will be from 11 ( if count from 1)
    * )
    * </code>
    * 
    * @param string 
    * @param array it may contains ORDER LIMIT ect.
    * @return array of arrays
    */
    public function fetch( $where = null, $options=null )
    {
        $result = array();
        if (is_string($where)) {
            $eval = $this->_prepareEvalWhere($where);
            foreach ($this as $key => $item) {
                try {
                    $isMatch = eval($eval); 
                } catch (Exception $exc) {
                    require_once 'Avz/DataStore/Exception.php';
                    throw new Avz_DataStore_Exception('Invalid eval: ' . $eval);   
                }
                if ($isMatch) {
                    $result[$key] = $item;
                }
            }
        }else{
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Invalid condition; please use string');               
        }
        
        if (isset( $options[self::KEY_ORDER])) {
            $orders = array_reverse(  $options[self::KEY_ORDER] );
            $this->_evalExprForSort = $this->_prepareEvalExprForSort($orders);
            usort($result, array($this , '_callbackSort') );
            $this->_evalExprForSort = null;
        }          
        
        if ( isset( $options[self::KEY_OFFSET] ) ) {
            $offset = $options[self::KEY_OFFSET]; 
            array_splice($result, 0, $offset);
        }    

        if (isset( $options[self::KEY_LIMIT] )) {
            $limit = $options[self::KEY_LIMIT];
            array_splice($result, $limit, count($result) - $limit);            
        }
        
        if ( isset( $options[self::KEY_FIELDS] ) ) {
            $fields = $options[self::KEY_FIELDS];
            $flipFildsArray = array_flip($fields);
            $intersectAndSortedFildsArray = array();
            foreach ($result as $key => $value) {
                $intersectFildsArray = array_intersect_key( $value, $flipFildsArray );
                $intersectAndSortedFildsArray[] = array_merge($flipFildsArray, $intersectFildsArray);
            }
            $result = $intersectAndSortedFildsArray;
        }
        return $result;
    }
     
     /**
     * Set identifier for item lookups
     *
     * @param  string|int|null $identifier
     * @return Avz_DataStore_Abstract
     */
    public function setIdentifier($identifier)
    {
        if (null === $identifier) {
            $this->_identifier = null;
        } elseif (is_string($identifier)) {
            $this->_identifier = $identifier;
        } elseif (is_numeric($identifier)) {
            $this->_identifier = (int) $identifier;
        } else {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Invalid identifier; please use a string or integer');
        }

        return $this;
    }
            
    /**
     * Retrieve current item identifier
     *
     * @return string|int|null
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Does an item with the given identifier exist?
     *
     * @param  string|int $id
     * @return bool
     */
    public function hasItem($id)
    {
        if (!is_array($this->_items)) {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Data is not inited. Array() and NULL are different');                
        }
        return array_key_exists($id, $this->_items);       
    }    
    
    /**
     * Retrieve an item by identifier
     *
     * Item retrieved will be flattened to an array.
     *
     * @param  string $id
     * @return array
     */
    public function getItem($id)
    {
        if (!$this->hasItem($id)) {
            return null;
        }

        return $this->_items[$id];        
    }
    /**
     * Add an individual item, optionally by identifier
     *
     * @param  array|object $item
     * @param  string|null $id
     * @return Avz_DataStore_Abstract
     */
    public function addItem($item, $id = null)
    {
        $item = $this->_normalizeItem($item, $id);
        if ($this->hasItem($item[self::DEFAULT_IDENTIFIER])) {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Overwriting items using addItem() is not allowed');
        }

        $this->_items[$item[self::DEFAULT_IDENTIFIER]] = $item[self::KEY_ITEM];

        return $this;        
    }
            
    /**
     * Set an individual item, optionally by identifier (overwrites)
     *
     * @param  array|object $item
     * @param  string|null $identifier
     * @return Avz_DataStore_Abstract
     */
    public function setItem($item, $id = null)
    {
        $item = $this->_normalizeItem($item, $id);
        $this->_items[$item[self::DEFAULT_IDENTIFIER]] = $item[self::KEY_ITEM];
        return $this;
    }
    
 
    /**
     * Remove item by identifier
     *
     * @param  string $id
     * @return Avz_DataStore_Abstract
     */
    public function removeItem($id)
    {
        if ($this->hasItem($id)) {
            unset($this->_items[$id]);
        }
        return $this;        
    }    
    
     /**
     * Get all items as an array
     *
     * Serializes items to arrays.
     *
     * @return array
     */
    public function getItems()
    {
        if (!is_array($this->_items)) {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Data not init. Array() and NULL are different');                
        }        
        return $this->_items;        
    }
    
    /**
     * Remove all items at once
     *
     * @return Avz_DataStore_Abstract
     */
    public function removeItems()
    {
        $this->_initItems();        
        $this->_items = array();
        return $this;        
    }   

    /**
     * Set label to use for displaying item associations
     *
     * @param  string|null $label
     * @return Avz_DataStore_Abstract
     */
    public function setLabel($label)
    {
        if (null === $label) {
            $this->_label = null;
        } else {
            $this->_label = (string) $label;
        }
        return $this;       
    }

    /**
     * Retrieve item association label
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->_label;        
    }

    /**
     * Load object from array
     *
     * @param  array $data
     * @return Avz_DataStore_Abstract
     */
    public function fromArray(array $data)
    {
        if (array_key_exists(self::KEY_IDENTIFIER, $data)) {
            $this->setIdentifier($data[self::KEY_IDENTIFIER]);
            unset($data[self::KEY_IDENTIFIER]);
        }
        if (array_key_exists(self::KEY_LABEL, $data)) {
            $this->setLabel($data[self::KEY_LABEL]);
            unset($data[self::KEY_LABEL]);      
        }
        if (array_key_exists(self::KEY_METADATA, $data)) {
            $metadata = $data[self::KEY_METADATA];
            unset($data[self::KEY_METADATA]);      
        }        
        if (array_key_exists(self::KEY_ITEMS, $data) && is_array($data[self::KEY_ITEMS])) {
            $this->setItems($data[self::KEY_ITEMS]);
            unset($data[self::KEY_ITEMS]);                 
        } else {
            $this->removeItems();
        }
        //rest - Metadata!
        
        return $this;        
    }
    
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
    public function itemsToArray()
    {
        $identifier = $this->getIdentifier();
        if (null === ($identifier)) {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Serialization requires that an identifier be present in the object; first call setIdentifier()');
        }
        $items = $this->getItems();
        $result = array();
        foreach ($items as $key => $item) {
            unset($item[$identifier]);
            $result[] = array_merge(array('id' => $key), $item);
        } 
        return $result;
    }
 
    /**
     * @return Json for dojo data object
     */
    public function itemsToJson()
    {    
        require_once 'Zend/Json.php';
        return Zend_Json::encode($this->itemsToArray());  
    }
    
    /**
     * Seralize entire data structure, including identifier and label, to array
     *
     * @return array
     */
    public function toArray()
    {
         $array = array();
        
        $identifier = $this->getIdentifier();
        if (null !== ($identifier)) {
            $array[self::KEY_IDENTIFIER] = $identifier;
        }else{
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Serialization requires that an identifier be present in the object; first call setIdentifier()');            
        }

        $metadata = $this->getMetadata();
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $array[$key] = $value;
            }           
        }

        if (null !== ($label = $this->getLabel())) {
            $array[self::KEY_LABEL] = $label;
        }
        
        if (!empty($this->_items)) {     
            $array[self::KEY_ITEMS] = array_values($this->getItems());
        }           

        return $array;
    }
    
     /**
     * Load object from JSON
     *
     * @param  string $json
     * @return Avz_DataStore_Abstract
     */
    public function fromJson($json)
    {
        if (!is_string($json)) {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('fromJson() expects JSON input');
        }
        require_once 'Zend/Json.php';
        $data = Zend_Json::decode($json);
        return $this->fromArray($data);
    }  
    
    /**
     * Serialize to JSON (dojo.data format)
     *
     * @return string
     */
    public function toJson()
    {
        require_once 'Zend/Json.php';
        return Zend_Json::encode($this->toArray());       
    }
   
//************************** ArrayAccess ************************    
 
    /**
     * ArrayAccess: does offset exist?
     *
     * @param  string|int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return (null !== $this->getItem($offset));
    }

    /**
     * ArrayAccess: retrieve by offset
     *
     * @param  string|int $offset
     * @return array
     */
    public function offsetGet($offset)
    {
        return $this->getItem($offset);
    }

    /**
     * ArrayAccess: set value by offset
     * 
     * <code>
     * Store[] = array('id' => 10, 'key' => 'val') -->> Store[10] = array('id' => 10, 'key' => 'val')
     * Store[8] = array('key' => 'val') -->>  Store[8] = array('id' => 8, 'key' => 'val')
     * Store[6] = array('id' => 10, 'key' => 'val') -->>  Store[6] = array('id' => 6, 'key' => 'val')
     * </code>
     * 
     * @param  string $offset
     * @param  array|object|null $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setItem($value, $offset);
    }

    /**
     * ArrayAccess: unset value by offset
     *
     * @param  string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->removeItem($offset);
    }

    
//************************** Iterator ************************
    /**
     * Iterator: get current value
     *
     * @return array
     */
    public function current()
    {
        return current($this->_items);
    }

    /**
     * Iterator: get current key
     *
     * @return string|int
     */
    public function key()
    {
        return key($this->_items);
    }

    /**
     * Iterator: get next item
     *
     * @return void
     */
    public function next()
    {
        return next($this->_items);
    }

    /**
     * Iterator: rewind to first value in collection
     *
     * @return void
     */
    public function rewind()
    {
        return reset($this->_items);
    }

    /**
     * Iterator: is item valid?
     *
     * @return bool
     */
    public function valid()
    {
        return (bool) $this->current();
    }

//********************** coutable *******************************    
    /**
    * @see coutable
    * @return int
     */
    public function count ()
    {
        return count($this->_items);        
    }        
//---------------------------------------------------------------

    /**
     * Normalize an item to attach to the collection
     *
     * @param  array|object $item
     * @param  string|int|null $id
     * @return array
     */
    protected function _normalizeItem($item, $id)
    {
        $identifier = $this->getIdentifier();
        
        if (null === $identifier) {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('You must set an identifier prior to adding items');
        }

        if (!is_object($item) && !is_array($item)) {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Only arrays and objects may be attached');
        }

        if (is_object($item)) {
            if (method_exists($item, 'toArray')) {
                $item = $item->toArray();
            } else {
                $item = get_object_vars($item);
            }
        }

        if ((null === $id) && !array_key_exists($identifier, $item)) {
            require_once 'Avz/DataStore/Exception.php';
            throw new Avz_DataStore_Exception('Item must contain a column matching the currently set identifier');
        } elseif (null === $id) {
            $id = $item[$identifier];
        } else {
            if (key_exists($identifier, $item)) {
                unset($item[$identifier]);            
                $item = array_merge(array($identifier => $id), $item);                     
            }else{
                $item[$identifier] = $id;               
            } 
        }

        return array(
            self::DEFAULT_IDENTIFIER   => $id,
            self::KEY_ITEM => $item,
        );
    }    

    /**
     * For resolve where condition in fetch()
     * 
     * @see fetch()
     * @return array
     */
    protected function _getReplacement()
    {
        $replacement =
            array(
                ' = ' => ' === ',
                ' IS NULL' =>' === null ',
                ' IS NOT NULL' => ' !== null ',
                ' => ' => ' >= '
            )
        ;
        return $replacement;
    }
   
    /**
     * Make Eval expr
     * 
     * For $where = "`f1` > 30 AND `f2` = `f1` OR `f3` IS NOT NULL " we get
     * "return  $item['f1']  > 30 AND  $item['f2']  ===  $item['f1']  OR  $item['f3']  !== null  ;"
     * 
     * @param array $where
     * @return string
     */
    protected function _prepareEvalWhere($where)
    {
        $replacement = $this->_getReplacement();        
        $woEscapedWhere = str_replace("\'", '#EscapedQuote', $where);
        $whereArray = explode("'", $woEscapedWhere);                
        $isString = FALSE;
        foreach ($whereArray as $key => $value) {
            if (!$isString) {
                $expressionArray = explode("`", $value);    
                $isFildName = FALSE;
                foreach ($expressionArray as  $keyExp => $subExpression) {
                    if ($isFildName) {
                        $fieldName = $subExpression;
                        $expressionArray[$keyExp] = ' $item[' . "'" . $fieldName . "'" . '] ';            
                    }else{
                        foreach ( $replacement as $from => $to) {
                            $subExpression = str_replace( $from , $to, $subExpression);   
                        }
                        $expressionArray[$keyExp] = $subExpression;
                    }
                    
                    $isFildName = !$isFildName;
                }
                $whereArray[$key] = implode($expressionArray);
            }       
            $isString = !$isString;
        }
        $where  = implode($whereArray, "'");        
        $result = str_replace( '#EscapedQuote', "\'", $where );   
        $result = 'return ' . $result . ';'; 
        return $result;        
    }            
    
    /**
     * Return same like "return  $item1['f2']  > $item2['f2']  && $item1['f1'] === $item2['f1']  || $item1['f1']  < $item2['f1']  ;"
     * 
     * @see fetch()
     * @param string|array $order array( '-f1', '+f2' )
     * @return array
     */
    protected function _prepareEvalExprForSort( $orders )
    {
        $evalExprForCompare = 'return ';
        while (!empty($orders)) {
           $carrentOrder = array_shift($orders);
           $fieldName = substr($carrentOrder, 1); 
           $direction = substr($carrentOrder, 0, 1);                
           if ( $direction === '+' ) {
               $operand = ' > ';
           }elseif ( $direction === '-' ) {
               $operand = ' < ';
           }else{
               require_once 'Avz/DataStore/Exception.php';
               throw new Avz_DataStore_Exception("Invalid order spec. ; please use array like array ('+id', '-fieldName', ...  ) "); 
           }
          $evalExprForCompare = $evalExprForCompare . ' $item1[\'' . $fieldName . '\'] ' . $operand . '$item2[\'' . $fieldName . '\'] ';                
          foreach ($orders as $order) {
              // array ('-fieldName', ...  )
              $otherFildName = substr($order, 1);
              $evalExprForCompare = $evalExprForCompare . ' &&' . ' $item1[\'' . $otherFildName . '\'] === $item2[\'' . $otherFildName . '\'] ';
          }
          $evalExprForCompare = $evalExprForCompare . ' ||';
        }
        $evalExprForCompare =  rtrim($evalExprForCompare, '|');
        $evalExprForCompare = $evalExprForCompare . ';';        
        return $evalExprForCompare;
    }    
    
    /**
     * 
     * @param array $item1 array( 'id' => 1 , 'fieldName' => ..  )
     * @param array $item2 array( 'id' => 4 , 'fieldName' => ..  )
     * @return int
     */
    protected function _callbackSort( $item1, $item2 )
    {
        if (eval($this->_evalExprForSort)) {
            return 1;
        }else{
            return -1;
        }
    }        
    
    /**
     * Init "data source" in dataStore
     * 
     * dataStore usually has "data source". It may be $this->_items array or
     * $this->_dbTable, ect. It needs in init. You can do it - just call 
     * setItems() with or without param. "Data source" can be inited only ones.
     * Usually - in constuct or in first call setItems(). 
     * Info for initialization may be contaned in {@link _metadata)
     * 
     * @return int
     */
    protected function _initItems()
    {
        if (!is_array($this->_items)) {
             $this->_items = array();
        }  
    }   
    
    
}