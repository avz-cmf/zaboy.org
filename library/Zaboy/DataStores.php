<?php
/**
 * Zaboy_DataStores
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

//require_once 'Avz/DataStore/Interface.php';
require_once 'Zaboy/Abstract.php';

/**
 * Zaboy_DataStores
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @todo Move identifier and label to $_metadata
 */
abstract class Zaboy_DataStores extends Zaboy_Abstract implements Countable, IteratorAggregate
{

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
    
//************************** IteratorAggregate ************************
    /**
    * @see IteratorAggregate 
    * @return IteratorAggregate
     */
    public function  getIterator()
    {
        return $this->_dataSource->getIterator();
    }   
}