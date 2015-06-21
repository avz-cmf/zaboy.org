<?php
/**
 * Zaboy_DataStores_Interface
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zaboy/DataStores/Read/Interface.php';
require_once 'Zaboy/DataStores/Write/Interface.php';

/**
 * Interface Zaboy_DataStores_Interface
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @see http://en.wikipedia.org/wiki/Create,_read,_update_and_delete 
 */
interface Zaboy_DataStores_Interface extends Zaboy_DataStores_Read_Interface, Zaboy_DataStores_Write_Interface, Traversable, Countable
{    
 
}