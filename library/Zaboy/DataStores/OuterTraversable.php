<?php
/**
 * Zaboy_DataStores_OuterTraversable
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/DataStores/Interface.php';
require_once 'Zaboy/DataStores/Abstract.php';

/**
 * class Zaboy_DataStores_OuterTraversable
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 */
class Zaboy_DataStores_OuterTraversable extends Zaboy_DataStores_Abstract  implements Zaboy_DataStores_Interface
{    
    /**
     * pointer for current item in iteration
     * 
     * @see Iterator
     * @var object $dataSource
     */   
    protected $_dataSource;   
    
    /**
     * 
     * @param Zaboy_Dic_ServicesConfigs $servicesConfigs
     * @return void
     */
    public function __construct ( $options, IteratorAggregate $dataSource )
    {
        $this->_dataSource = $dataSource;
        parent::__construct($options);
    }    
    
//** Interface "IteratorAggregate" **             **                          **
    
    /**
     * @see IteratorAggregate 
     * @return IteratorAggregate
     */
    public function  getIterator() {
        return $this->_dataSource->getIterator();
    }
}