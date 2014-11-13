<?php
/**
 * Avz_DataStores_Abstract
 * 
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

//require_once 'Avz/DataStore/Interface.php';
require_once 'Avz/Abstract.php';

/**
 * Avz_DataStores_Abstract
 * 
 * <b>DataStore</b><br>
 * <i>DataStore</i> ready work with data just after creating.<br>
 *  <i>Metadata</i>is changeable after setting , but it is not recommended.<br>
 * <b>DataStore</b> is included:
 * <ul>
 * <li><b>DataSource</b> - object with data (it may be table, REST service, ect ...). 
 *        It must has primary key. See {@link Avz_DataSources_Abstract}</li>
 * 
 * <li><b>Metadata</b> - It is important info about DataStore  (behavior rules, character encoding ...).
 *        You can set <i>Metadata</i> in $options via {@link __construct()}
 * <code>
 *  new Avz_DataStore( array Avz_DataStore_Abstract::KEY_METADATA => array('key' => 'val') )
 * </code>
 * or in application.ini
 * <code>
 *  resources.dic.dsDataStoreDbTable.dbtDbTableForSource.class = Awz_Db_Table
 *  resources.dic.dsDataStoreDbTable.dbtDbTableForSource.options.name = "my-table"
 *  resources.dic.service.dsoSource.class = 'Avz_DataSource_DbTable'
 *  resources.dic.service.dstStoreDbTable.class = 'Avz_DataStore_DbTable'
 *  resources.dic.service.dstStoreDbTable.options.metadata.key = val
 * </code>
 * and after it - get object of Store from Dic
 * <code>
 *  global $application;
 *  $bootstrap = $application->getBootstrap();
 *  $dic = $bootstrap->getResource('dic');
 *  $dsDataStoreDbTable = $dic->get('dstStoreDbTable');   
 * </code>
 *
 * <li> <b>All rest</b>  will be in attribs. See {@link getAttrib()}</li>
 * </ul>
 * 
 * @see Avz_DataStore_Interface
 * @category   DataStores
 * @package    DataStores
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 * @todo Move identifier and label to $_metadata
 */
abstract class Avz_DataStores_Abstract extends Avz_Abstract implements Countable
{

    /**
     * Default identifier
     * 
     * @see getIdentifier()
     */
    const DEFAULT_IDENTIFIER = 'id';
    
//********************** coutable *******************************    
    /**
    * @see coutable
    * @return int
     */
    public function count()
    {
        try {
            $keys = $this->_dataSource->getKeys();
        } catch (Exception $exc) {
            require_once 'Avz/DataStores/Exception.php';
            throw new Avz_DataStores_Exception(
                    __METHOD__ . ' throws Exception.',
                    0,
                    $exc
            );
        }
        return count($keys);
    }        
}