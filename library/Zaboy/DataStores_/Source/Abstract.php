<?php
/**
 * Zaboy_DataStore_Source_Abstract
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Zaboy_DataStore_Source_Abstract
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
abstract class  Zaboy_DataStores_Source_Abstract extends Zaboy_Services
{
//http://php.ru/manual/class.arrayiterator.html
 
    /**
     * Default identifier
     * 
     * @see getIdentifier()
     */
    const DEFAULT_IDENTIFIER = 'id';
    
    /**
     * Retrieve identifier (name of primary key)
     *
     * @return string|int
     */
    public function getIdentifier()
    {
        return self::DEFAULT_IDENTIFIER;
    }
    

}