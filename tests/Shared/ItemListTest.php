<?php
namespace ApiAxleTest\Shared;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\ItemList;

class ItemListTest extends \PHPUnit_Framework_TestCase
{
    public function testItemList()
    {
        $itemList = new ItemList();
        $itemList->addItem('item1');
        $itemList->addItem('item2');
        $itemList->addItem('item3');
        $itemList->addItem('item4');
        $this->assertEquals(4,$itemList->size());
        
        foreach($itemList as $item){
            $current = $itemList->current();
            $this->assertTrue(is_string($current));
        }
        
        $this->assertTrue(is_array($itemList->getItemsArray()));
    }
}