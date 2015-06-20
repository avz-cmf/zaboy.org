<?php
/**
 * Zaboy_DataStores_Read_Finder_Interface
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/DataStores/Read/Interface.php';

/**
 * Interface Zaboy_DataStores_Read_Finder_Interface
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 * @see http://rudocs.exdat.com/docs/index-567188.html?page=5
 */
interface Zaboy_DataStores_Read_Finder_Interface
{
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
    
    /**
     * Callback functionm for array sorting
     * 
     * @param array $itemA
     * @param array $itemB
     */
    public function compare($itemA, $itemB);
 
    /**
     * Callback functionm for array sorting
     * 
     * @param array $itemA
     * @param array $itemB
     */
    public function _sort($items);
    
    
    
    
}