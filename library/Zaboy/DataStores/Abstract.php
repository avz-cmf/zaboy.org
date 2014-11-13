<?php
/**
 * Zaboy_DataStores_Abstract
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

//require_once 'Avz/DataStore/Interface.php';
require_once 'Zaboy/DataStores/Abstract.php';

/**
 * Zaboy_DataStores_Abstract

 * @see Avz_DataStore_Interface
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @todo Move identifier and label to $_metadata
 */
abstract class Zaboy_DataStores_Abstract extends Zaboy_Abstract implements 
    Zaboy_DataStores_Adapter_Read_Interface, 
    IteratorAggregate
{
    /**
     * @see _metadata
     */
    const KEY_METADATA = 'metadata';   

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
     * Retrieve current item identifier
     *
     * @return string|int|null
     */
    public function getIdentifier();
    
    /**
     * Return list of keys
     * 
     * @return array
     */
    public function getKeys();

    /**
     * Does an item with the given identifier exist?
     *
     * @param int $id
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
     * Get all items as an array
     *
     * Items retrieved will be flattened to an array of arrays.
     *
     * @return array
     */
    public function getItems(); 
    
    
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