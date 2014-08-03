<?php
/**
 * Avz_DataSources_Aggregate_Iterator
 * 
 * @category   DataSources
 * @package    DataSources
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Avz_DataSources_Aggregate_Iterator
 * 
 * @category   DataSources
 * @package    DataSources
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Avz_DataSources_Aggregate_Iterator extends Avz_Abstract implements Iterator
{  
    /**
     * @var Avz_DataSources_Abstract 
     */
    protected $_owner = null;
    
    /**
     * pointer in array of children  $this->_owner->getItem[$this->index];
     * @var mix $index
     */   
    public $index = null;
    
    /**
     * 
     * @param Avz_DataSources_Interface $_owner
     */
    public function setOwner($owner)
    {
        $this->index = null;
        $this->_owner = $owner;
    }
//************************** Iterator ************************
   
    /**
    * @see Iterator
    * @return void
    */
    public function rewind()
    {
        $keys = $this->_owner->getKeys();
        if ( empty($keys)) {
            $this->index =  null;
            return null;
        }else{
            asort($keys);
            $this->index =  array_shift($keys);
            return $this->index;
        }
    }

    /**
    * @see Iterator
    * @return array
    */
    public function current()
    {
        if ( !is_null($this->index) && $this->_owner->retriveItem($this->index)!== null ) {
            return $this->_owner->retriveItem($this->index);
        }else{
            return null;
        } 
    }

    /**
    * @see Iterator
    * @return int
    */
    public function key()
    {
        return $this->index;
    }

    /**
    * @see Iterator
    * @return array
    */
    public function next()
    {
        $keys = $this->_owner->getKeys();
        if ( empty($keys)) {
            return null;
        }else{
            asort($keys);
            foreach ($keys as $id) {
                if ($id > $this->index) {
                    $this->index = $id;
                    return $this->_owner->retriveItem($this->index);
                }
            }
            $this->index = null;
            return null;
        }
    }

    /**
    * @see Iterator
    * @return bool
    */
    public function valid()
    {
        return !is_null($this->index) && $this->_owner->retriveItem($this->index)!== null;
    }
} 
