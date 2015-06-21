<?php
/**
 * Zaboy_Service_Db_Table
 * 
 * @category   Avz
 * @package    Model
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once 'Zend/Db/Table/Abstract.php';

/**
 * Абстрактный класс таблицы
 * 
 * @category   Avz
 * @package    Model
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @uses Zend Framework from Zend Technologies USA Inc.
 */
class Zaboy_Service_Db_Table() extends Zend_Db_Table_Abstract 
{
    parent::__construct();
}