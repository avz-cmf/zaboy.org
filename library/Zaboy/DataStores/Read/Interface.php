<?php
/**
 * Zaboy_DataStores_Read_Interface
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Interface Zaboy_DataStores_Read_Interface
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 * @see http://rudocs.exdat.com/docs/index-567188.html?page=5
 */
interface Zaboy_DataStores_Read_Interface
{    

    /**
     * Default identifier
     * 
     * @see getIdentifier()
     */
    const DEF_ID = 'id';
    
    /**
     * sorting by ascending
     */
    const ASC = '+';
    
    /**
     * sorting by descending
     */
    const DESC = '-';    
    
    
    /**
     * Return Item by id
     * 
     * Method return null if item with that id is absent.
     * Format of Item - Array("id"=>123, "fild1"=value1, ...)
     * 
     * @param int|string $id PrimaryKey
     * @return array|null
     */
    public function read($id);
    
     /**
     * Return true if item with that id is present.
     * 
     * @param int|string $id PrimaryKey
     * @return bool
     */
    public function has($id);   
    
    /**
     * Return items by criteria with mapping, sorting and paging
     * 
     * Example:
     * <code>
     * find(
     *    array(self::DEF_ID), // return only identifiers
     *    array('fild2' = 2, 'fild5' = 'something'), // 'fild2' === 2 && 'fild5 === 'something' 
     *    array(self::DEF_ID => self::DESC),  // Sorting in reverse order by 'id" fild
     *    10, // not more then 10 items
     *    5 // from 6th items in result set (first item is 0th)
     * ) 
     * </code>
     * 
     * @see ASC
     * @see DESC
     * @param Array $filds What filds will be included in result set. All by default 
     * @param Array $where
     * @param Array $order
     * @param int $limit
     * @param int $offset
     */
    public function find(
        $filds = array(), 
        $where = array(), 
        $order = array(),            
        $limit = 0, 
        $offset = 0 
    );

    
//** Interface "IteratorAggregate" **             **                          **
    
    /**
    * @see IteratorAggregate 
    * @return IteratorAggregate
     */
    public function  getIterator();
    
    
//** Interface "Coutable" **                      **                          **
    
    /**
     * @see coutable
     * @return int
     */
    public function count();    
}