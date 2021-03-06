<?php
/**
* Zaboy_DataStore_AbstractTest
*
*<b>This is test for {@see Zaboy_DataStore_Abstract}</b>
*
* @category Test
* @package Test
* @copyright Zaboychenko Andrey
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

require_once 'Zaboy/DataStores/Abstract.php';

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-08-25 at 15:44:45.
 */
abstract class Zaboy_DataStore_AbstractTest extends PHPUnit_Framework_TestCase {

    /**
     * @var array
     */
    protected $_optionsDelault = array();

    /**
     * @var Zaboy_DataStores_Abstract
     */
    protected $object;
    
    /**
     *
     * @var array 
     */

    protected $_itemsArrayDelault  =    
        array(
            array( 'id' => 1,'anotherId' => 10, 'fString' => 'val1', 'fFloat' => 400.0004),
            array( 'id' => 2,'anotherId' => 20, 'fString' => 'val2', 'fFloat' => 300.003),
            array( 'id' => 3,'anotherId' => 40, 'fString' => 'val2', 'fFloat' => 300.003),  
            array( 'id' => 4,'anotherId' => 30, 'fString' => 'val2', 'fFloat' => 100.1)          
        );
    

    protected $_itemsArrayEnhanced  =    
        array(
            array( 'id' => 1,'anotherId' => 10, 'fString' => 'val1', 'fFloat' => 400.0004, 'nll' => 1,      'abs' => 'val_abs'),
            array( 'id' => 2,'anotherId' => 20, 'fString' => 'val2', 'fFloat' => 300.003,  'nll' => null),
            array( 'id' => 3,'anotherId' => 40, 'fString' => 'val2', 'fFloat' => 300.003,  'nll' => null),
            array( 'id' => 4,'anotherId' => 30, 'fString' => 'val2', 'fFloat' => 100.1 ,   'nll' => null)
        );
    

    protected $_itemsArrayNull  =    
        array(
            array( 'id' => 1, 'nll' => 1,      'abs' => 'val1'  ),
            array( 'id' => 2, 'nll' => null                     ),
            array( 'id' => 3, 'nll' => null                     ),  
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
     * This method init $this->object
     */
    abstract protected function _initObject( $options = null, $data = null );
 
/**************************** Identifier ************************/
    
    public function testSetIdentifier() {
        $this->_initObject();
        $this->assertEquals(
            'id', $this->object->getIdentifier()
        );
    }    
// *************************** Item **************************************************      
    public function testRead_defaultId()
    {
        $this->_initObject();         
        $this->assertEquals(
                $this->_itemsArrayDelault[2-1] ,
                $this->object->read(2)
        );
        
        $this->assertEquals(
                $this->_itemsArrayDelault['1'] ,
                $this->object->read('2')
        );        
    }
    
    public function testHas_defaultId()
    {
        $this->_initObject();         
        $this->assertTrue($this->object->has(2));
        $this->assertFalse($this->object->has(20));        
    } 
    
    public function testGetKeys_4()
    {
        $this->_initObject();         
        $keys = $this->object->GetKeys();
        $this->assertEquals(
                array(1,2,3,4),
                $keys
        );  
    } 
    
    public function testGetKeys_0()
    {
        $this->_initObject();         
        $this->object->deleteAll();
        $keys = $this->object->GetKeys();
        $this->assertEquals(
                array(),
                $keys
        );  
    } 
    
    public function testFind_all()
    {
        $this->_initObject();
        $findArray = $this->object->find();        
        for ($index = 0; $index < count($this->_itemsArrayDelault); $index++) {
            $this->assertEquals(
                    array_pop($this->_itemsArrayDelault),
                    array_pop($findArray)
            );               
        } 
    }    
    
    public function testFind_orderId()
    {
        $this->_initObject();
        $findArray = $this->object->find(null, null, array('id'=>'ASC'));        
        for ($index = 0; $index < count($this->_itemsArrayDelault); $index++) {
            $this->assertEquals(
                    array_pop($this->_itemsArrayDelault),
                    array_pop($findArray)
            );               
        } 
    }

    public function testFind_orderAnotherId()
    {
        $this->_initObject();
        $findArray = $this->object->find(null, null, array('anotherId'=>'ASC'));        
            $this->assertEquals(
                    array_pop($this->_itemsArrayDelault),
                    $findArray[3-1]
            );        
            $this->assertEquals(
                    array_pop($this->_itemsArrayDelault),
                    $findArray[4-1]
            );    
    }    
 
    public function testFind_orderDesc()
    {
        $this->_initObject();
        $findArray = $this->object->find(null, null, array('id'=>'DESC'));        
        $this->assertEquals(
                $this->_itemsArrayDelault[1-1] ,
                $findArray[4-1]
        );     
        $this->assertEquals(
                $this->_itemsArrayDelault[2-1] ,
                $findArray[3-1]
        );   
    }   
    
    public function testFind_orderCombo()
    {
        $this->_initObject();
        $findArray = $this->object->find(null, null, array('fString'=>'DESC', 'fFloat'=>'ASC', 'anotherId'=>'DESC'));        
        $this->assertEquals(
                $this->_itemsArrayDelault[4-1] ,
                $findArray[1-1]
        );     
        $this->assertEquals(
                $this->_itemsArrayDelault[3-1] ,
                $findArray[2-1]
        );
        $this->assertEquals(
                $this->_itemsArrayDelault[1-1] ,
                $findArray[4-1]
        );   
    }    

    public function testFind_WhereId()
    {
        $this->_initObject();
        $findArray = $this->object->find(array('id'=> 2));        
        $this->assertEquals(
                $this->_itemsArrayDelault[2-1] ,
                $findArray[1-1]
        );     
        $this->assertEquals(
                1,
                count($findArray)
        );
    } 
    
    public function testFind_WhereCombo()
    {
        $this->_initObject();
        $findArray = $this->object->find(
                array('fString'=> 'val2', 'fFloat' => 300.003),
                null,
                array('id'=>'ASC')
         );        
        $this->assertEquals(
                $this->_itemsArrayDelault[2-1] ,
                $findArray[1-1]
        );     
        $this->assertEquals(
                2,
                count($findArray)
        );
    }  
    
    
    public function testFind_fildsCombo()
    {
        $this->_initObject();
        $findArray = $this->object->find(
                array('fString'=> 'val2', 'fFloat' => 300.003),
                array('fFloat'),
                array('id'=>'ASC')
         );        
        $this->assertEquals(
                array('fFloat' => $this->_itemsArrayDelault[2-1]['fFloat']) ,
                $findArray[1-1]
        );     
        $this->assertEquals(
                2,
                count($findArray)
        );
    }  
    
    public function testFind_limitCombo()
    {
        $this->_initObject();
        $findArray = $this->object->find(
                array('fString'=> 'val2', 'fFloat' => 300.003),
                array('fFloat'),
                array('id'=>'ASC'),
                1
         );        
        $this->assertEquals(
                array('fFloat' => $this->_itemsArrayDelault[2-1]['fFloat']) ,
                $findArray[1-1]
        );     
        $this->assertEquals(
                1,
                count($findArray)
        );
    }  
    
    
    public function testFind_offsetCombo()
    {
        $this->_initObject();
        $findArray = $this->object->find(
                array('fString'=> 'val2', 'fFloat' => 300.003),
                array('fFloat'),
                array('id'=>'ASC'),
                null,
                1
         );        
        $this->assertEquals(
                array('fFloat' => $this->_itemsArrayDelault[3-1]['fFloat']) ,
                $findArray[1-1]
        );     
        $this->assertEquals(
                1,
                count($findArray)
        );
    } 
    
    public function testFind_limitOffsetCombo()
    {
        $this->_initObject();
        $findArray = $this->object->find(
                array('fString'=> 'val2'),
                array('fFloat'),
                array('id'=>'ASC'),
                2,
                1
         );        
        $this->assertEquals(
                array('fFloat' => $this->_itemsArrayDelault[3-1]['fFloat']) ,
                $findArray[1-1]
        );  
        $this->assertEquals(
                array('fFloat' => $this->_itemsArrayDelault[4-1]['fFloat']) ,
                $findArray[2-1]
        ); 
        $this->assertEquals(
                2,
                count($findArray)
        );
    } 
    

    public function testFind_Enhanced()
    {
        $this->_initObject(array(),$this->_itemsArrayEnhanced);
        $findArray = $this->object->find(
                array('abs'=> 'val_abs')
         );
        $this->assertEquals(
                1,
                count($findArray)
        );
    }         
    
    public function testCreate_withoutId()
    {
        $this->_initObject();
        $id = $this->object->create(
            array(
                'fFloat' => 1000.01,
                'fString'=> 'Create_withoutId'
            )
        );
        $insertedItem = $this->object->read($id);
        $this->assertEquals(
                'Create_withoutId',
                $insertedItem['fString']
        );
        $this->assertEquals(
                1000.01 ,
                $insertedItem['fFloat']
        );
    }

    public function testCreate_withtId()
    {
        $this->_initObject();
        $id = $this->object->create(
            array(
                'id' => 1000,
                'fFloat' => 1000.01,
                'fString'=> 'Create_withId'
            )
        );
        $insertedItem = $this->object->read($id);
        $this->assertEquals(
                'Create_withId',
                $insertedItem['fString']
        );
        $this->assertEquals(
                1000 ,
                $id
        );
    }
    
    public function testCreate_withtIdRewrite()
    {
        $this->_initObject();
        $id = $this->object->create(
            array(
                'id' => 2,
                'fString'=> 'Create_withtIdRewrite'
            ),
            true    
        );
        $insertedItem = $this->object->read($id);
        $this->assertEquals(
                'Create_withtIdRewrite',
                $insertedItem['fString']
        );
        $this->assertEquals(
                2 ,
                $id
        );
    }

    public function testCreate_withtIdRewriteException()
    {
        $this->_initObject();
        $this->setExpectedException('Zaboy_DataStores_Exception');    
        $this->object->create(
            array(
                'id' => 2,
                'fString'=> 'Create_withtIdRewrite'
            ),
            false    
        );
    }
    
   
    public function testUpdate_withoutId()
    {
        $this->_initObject();
        $this->setExpectedException('Zaboy_DataStores_Exception');    
        $id = $this->object->update(
            array(
                'fFloat' => 1000.01,
                'fString'=> 'Create_withoutId'
            )
        );
    }

    public function testUpdate_withtId_WhichPresent()
    {
        $this->_initObject();
        $count = $this->object->update(
            array(
                'id' => 3,
                'fString'=> 'withtId_WhichPresent'
            )
        );

        $item = $this->object->read(3);
        $this->assertEquals(
            40,    
            $item['anotherId']
                
        );
        $this->assertEquals(
            'withtId_WhichPresent',    
            $item['fString']
                
        );
        $this->assertEquals(
                1 ,
                $count
        );
    }


    public function testUpdate_withtId_WhichAbsent()
    {
        $this->_initObject();
        $count = $this->object->update(
            array(
                'id' => 1000,
                'fFloat' => 1000.01,
                'fString'=> 'withtIdwhichAbsent'
            )
        );
        $this->assertNull(
            $this->object->read(1000)
        );
        $this->assertEquals(
                0 ,
                $count
        );
    }
    

    public function testUpdate_withtIdwhichAbsent_ButCreateIfAbsent_True()
    {
        $this->_initObject();
        $count = $this->object->update(
            array(
                'id' => 1000,
                'fFloat' => 1000.01,
                'fString'=> 'withtIdwhichAbsent'
            ),
            true    
        );
        $item = $this->object->read(1000);
        $this->assertEquals(
            'withtIdwhichAbsent',    
            $item['fString']
                
        );
        $this->assertEquals(
                1 ,
                $count
        );
    }  
    

    public function testDelete_withtId_WhichAbsent()
    {
        $this->_initObject();
        $count = $this->object->delete(1000);
        $this->assertEquals(
                0 ,
                $count
        );
    }
    

    public function testDelete_withtId_WhichPresent()
    {
        $this->_initObject();
        $count = $this->object->delete(4);
        $this->assertEquals(
                1 ,
                $count
        );
        $this->assertNull(
            $this->object->read(4)
        );
    }
    
     public function testDelete_withtId_Null()
    {
        $this->_initObject();
        $this->setExpectedException('Zaboy_DataStores_Exception');  
        $count = $this->object->delete(null);
    }
    
     public function testDeleteAll()
    {
        $this->_initObject();
        $count = $this->object->deleteAll();
        $this->assertEquals(
                4 ,
                $count
        );
        $count = $this->object->deleteAll();
         $this->assertEquals(
                0 ,
                $count
        );
    }
    
    
    
     public function testCount_count4()
    {
        $this->_initObject();
        $count = $this->object->count();
        $this->assertEquals(
                4 ,
                $count
        );
    }
     
     public function testCount_count0()
    {
        $this->_initObject();
        $count = $this->object->deleteAll();
        $count = $this->object->count();
        $this->assertEquals(
                0 ,
                $count
        );
    }  
    
/**    

    
  
  
    
   
    public function testSetItem_withoutId()
    {
        $this->_initObject();         
        //$this->object->setIdentifier('id');
        $this->object->setItem($this->itemsData[1]);
        $itemWithouId = $this->itemsData[2];
        $this->object->setItem($itemWithouId);        
        unset($itemWithouId['id']);
        $this->object->setItem($itemWithouId);
        $this->object->setItem($itemWithouId);        
 
        $this->assertEquals(
                4,
                $this->object->count()
        );   
    }     
     
     
    public function testRemoveItemt_One()
    {
        $this->_initObject();         
        //$this->object->setIdentifier('id');
        $this->object->setItem($this->itemsData[1]);  
        $this->object->setItem($this->itemsData[2]);         
        $this->object->removeItem(2);
        $this->assertEquals(
                1,
                $this->object->count()
        );         
    } 
/**   
    public function testRemoveItemts()
    {
        
        $this->_initObject();         
        //$this->object->setIdentifier('id');
        $this->object->setItem($this->itemsData[1]);  
        $this->object->setItem($this->itemsData[2]);     
        $this->object->setItem($this->itemsData[3]); 
        $this->object->setItem($this->itemsData[4]); 

        
         $this->assertEquals(
                4,
                $this->object->count()
        );            
        $this->object->removeItems();

        $this->assertEquals(
                0,
                $this->object->count()
        );         
    }     
* / 
//************************** Iterator ************************ 
    
    public function testIteratorInterfaceStepToStep()
    {
        $this->_initObject();         
        //$this->object->setIdentifier('id');
        foreach ($this->itemsData as $value) {
             $this->object->setItem($value);
        }
        $this->assertEquals(
                4,
                $this->object->count()
        );
        foreach ($this->itemsData as $key => $value) {
            $this->assertEquals( $value, $this->object->getItem($key) );
        }
    
        $i = 0;
        foreach ($this->object as $key => $value) {
            $i = $i +1;
            $this->assertEquals(
                    $key,
                    $i
            );            
            $this->assertEquals( $value, $this->itemsData[$i] );
        }
        $this->assertEquals( 4, $i );        
        
    }     
    
    
    public function testIteratorInterfaceEditedData()
    {
        $this->_initObject();         
        //$this->object->setIdentifier('id');
        foreach ($this->itemsData as $value) {
             $this->object->setItem($value);
        }
        $this->assertEquals(
                4,
                $this->object->count()
        );
        foreach ($this->itemsData as $key => $value) {
            $this->assertEquals(
                    $value,
                    $this->object->getItem($key)
            );
        }
        
        $this->object->removeItem(2);
        $newItem = $this->itemsData[2];
        unset($newItem['id']);
        $this->object->setItem($newItem); 

        $i = 0;
        foreach ($this->object as $key => $value) {
            $i = $i +1;
            $this->assertEquals(
                    $value['fString'],
                    $this->itemsData[$value['anotherId']/10]['fString']
            );
        }        
        $this->assertEquals( 4, $i );          
    }         
    
//************************* Countable **********************
    
    public function testShouldImplementCountable()
    {
        $this->assertTrue(is_a($this->testedClass, 'Countable', true));
    }
    
    public function testCountNull()
    {
        $this->_initObject();         
        $this->assertEquals(
                0,
                $this->object->count()
        ); 
    }
    
    public function testCount2()
    {
        $this->_initObject();  
        $this->object->setItem($this->itemsData[1]);
        $this->object->setItem($this->itemsData[2]);
        $this->assertEquals(
                2,
                $this->object->count()
        ); 
    }    
    
    /**
    public function testAddItemShouldAcceptStdObject()
    {
        $this->_initObject('optionsDataStandart');         
        $item = array( 'id' => 1000, 'fString' => 'val10', 'fInt' => 100);
        $obj = (object) $item;
        $this->object->setIdentifier('id');
        $this->object->addItem($obj);
        $this->assertEquals(1, count($this->object));
        $this->assertSame($item, $this->object->getItem(1000));
    }   
     * /
     */
}
