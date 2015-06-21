<?php
/**
 * Zaboy_DataStores_InnerTraversable
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/DataStores/Interface.php';
require_once 'Zaboy/DataStores/Abstract.php';

/**
 * class Zaboy_DataStores_InnerTraversable
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 */
class Zaboy_DataStores_InnerTraversable extends Zaboy_DataStores_Abstract  implements Zaboy_DataStores_Interface
{  
    /**
     * pointer for current item in iteration
     * 
     * @see Iterator
     * @var mix $index
     */   
    protected $index = null;    
    
//**   Interface "Iterator"  **             **                          **
   
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