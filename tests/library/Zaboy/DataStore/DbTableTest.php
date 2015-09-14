<?php
/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-06-21 at 20:50:59.
 */
class Zaboy_DataStore_DbTableTest extends Zaboy_DataStore_AbstractTest {
    /**
     * @var Zaboy_DataStore_DbTable
     */
    protected $object;
    
    protected $dbTableName ='data_store_test';       
    protected $tableClass ='Zend_Db_Table';    
    
    protected $configTableDefault = array(
                                        'id' => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
                                        'anotherId' => 'INT NOT NULL',
                                        'fString' => 'CHAR(20)',
                                        'fInt' => 'INT'        
                                    );   
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
    }
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
     /**
      * 
      * @param array $data
      */
    protected function _getDbTableFilds( $data ) {
        $record = array_shift($data);
        reset($record);
        $firstKey = key($record);
        $firstValue = array_shift($record);
        $dbTableFilds = '';
        if ( is_string($firstValue) ) {
                $dbTableFilds =  '`' . $firstKey . '` CHAR(80) PRIMARY KEY';
            }elseif (is_integer($firstValue)) {
                $dbTableFilds = '`' . $firstKey . '` INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
        }else{
             trigger_error ("Type of primary key must be int or string", E_USER_ERROR);
        } 
        foreach ($record as $key => $value) {
            if (is_string($value)) {
                $fildType = ', `' . $key . '` CHAR(80)';
            }elseif (is_integer($value)) {
                $fildType =  ', `' . $key . '` INT';
            }elseif (is_float($value)) {
                $fildType =  ', `' . $key . '` DOUBLE PRECISION';
            }else{
                trigger_error ("Type of fild of array isn't supported.", E_USER_ERROR);
            }
            $dbTableFilds = $dbTableFilds . $fildType;
        }    
        return $dbTableFilds;
    }   
    
    /**
     * This method init $this->object
     */
    protected function _prepareTable( $data ) {
        global $bootstrap;
        $db = $bootstrap->getResource('db');
        
        $deleteStatement = "DROP TABLE IF EXISTS `$this->dbTableName`;";
        $db->query($deleteStatement); 
        
        $createStatement = "CREATE TABLE `$this->dbTableName` ";
        $filds = $this->_getDbTableFilds( $data );
        $createStatement = $createStatement .  '(' . $filds . ') ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;';
        $db->query($createStatement); 
       
    }
    
    /**
     * This method init $this->object
     */
    protected function _initObject( $options = null, $data = null ) {
        $filename = APPLICATION_PATH . '/../tests/application/configs/application.ini' ;
        $application = new Zend_Application( APPLICATION_ENV , $filename);
        $application->bootstrap();
        global $bootstrap;        
        $bootstrap =  $application->getBootstrap();
        
        if (is_null($options)) {
            $options = $this->_optionsDelault;
        }
        if (is_null($data)) {
            $data = $this->_itemsArrayDelault;
        } 
        $this->_prepareTable($data);
        $dbTable = new Zend_Db_Table( array( 'name' => $this->dbTableName) );
        
        foreach ($data as $record) {
            $newRow = $dbTable->createRow($record);
            // INSERT the new row to the database
            $newRow->save();        
        } 
        
        $this->object = new Zaboy_DataStore_DbTable($options, $dbTable);
    }
/**************************** Identifier ************************/

}
