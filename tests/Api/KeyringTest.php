<?php
namespace ApiAxleTest\Api;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\Config;
use ApiAxle\Api\Key;
use ApiAxle\Api\Api;
use ApiAxle\Api\Keyring;
use ApiAxle\Shared\ApiException;

class KeyringTests extends \PHPUnit_Framework_TestCase
{
    public static function tearDownAfterClass()
    {
        try {
            $key = new Key();
            $keyList = $key->getList();
            foreach($keyList as $item){
                if(strpos($item->getKey(),'test-') !== false){
                    $key->delete($item->getKey());
                }
            }

            $keyring = new Keyring();
            $keyringList = $keyring->getList();
            foreach($keyringList as $item){
                if(strpos($item->getName(),'test-') !== false){
                    $keyring->delete($item->getName());
                }
            }
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
    }
    
    public function testCreateKeyring()
    {
        $keyringName = 'test-'.str_replace(array(' ','.'),'',microtime());
        $keyring = new Keyring();
        $keyring->create($keyringName);
        $this->assertInternalType('integer', $keyring->getCreatedAt());
    }
    
    public function testGetKeyringList()
    {
        $keyring = new Keyring();
        $keyringList = $keyring->getList();
        //print_r($keyringList);
        $this->assertInstanceOf('ApiAxle\Shared\ItemList', $keyringList);
    }
    
    public function testCreateDeleteKeyring()
    {
        $keyringName = 'test-'.str_replace(array(' ','.'),'',microtime());
        $keyring = new Keyring();
        $keyring->create($keyringName);
        $this->assertInternalType('integer', $keyring->getCreatedAt());
        $keyringList = $keyring->getList();
        $hasKeyring = false;
        foreach($keyringList as $item){
            if($item->getName() == $keyringName){
                $hasKeyring = true;
                break;
            }
        }
        $this->assertTrue($hasKeyring,'Keyring not found after create');
        
        $getKeyring = $keyring->get($keyringName);
        $this->assertTrue(is_integer($getKeyring->getCreatedAt()));
        
        $keyring->delete();
        $keyringList = $keyring->getList();
        $hasKeyring = false;
        foreach($keyringList as $item){
            if($item->getName() == $keyringName){
                $hasKeyring = true;
                break;
            }
        }
        $this->assertFalse($hasKeyring,'Keyring still found after delete');
    }
    
    public function testLinkUnlinkKey()
    {
        $keyValue = 'test-'.str_replace(array(' ','.'),'',microtime());
        $key = new Key();
        $key->create($keyValue);
        
        $keyringName = 'test-'.str_replace(array(' ','.'),'',microtime());
        $keyring = new Keyring();
        $keyring->create($keyringName);
        $keyring->linkKey($key);
        
        $keyList = $keyring->getKeyList();
        $hasKey = false;
        foreach($keyList as $item){
            if($item->getKey() == $keyValue){
                $hasKey = true;
                break;
            }
        }
        $this->assertTrue($hasKey,'Key not found after linking');
        
        $keyring->unLinkKey($key);
        $keyList = $keyring->getKeyList();
        $hasKey = false;
        foreach($keyList as $item){
            if($item->getKey() == $keyValue){
                $hasKey = true;
                break;
            }
        }
        $this->assertFalse($hasKey,'Key still found after unlinking');
    }
    
    public function testGetStats()
    {
        $keyringName = 'test-'.str_replace(array(' ','.'),'',microtime());
        $keyring = new Keyring();
        $keyring->create($keyringName);
        $stats = $keyring->getStats(time()-1000,time());
        $this->assertInstanceOf('\stdClass', $stats);
    }
    
    public function testBatchLinkKeys()
    {
        $key1 = new Key();
        $key1->create('test-1'.str_replace(array(' ','.'),'',microtime()));
        
        $key2 = new Key();
        $key2->create('test-2'.str_replace(array(' ','.'),'',microtime()));
        
        $key3 = new Key();
        $key3->create('test-3'.str_replace(array(' ','.'),'',microtime()));
        
        $keys = array($key1,$key2,$key3);
        
        $keyring = new Keyring();
        $keyring->create('test-r'.str_replace(array(' ','.'),'',microtime()), $keys);
        
        $keyList = $keyring->getKeyList();
        $this->assertInstanceOf('ApiAxle\Shared\ItemList', $keyList);
        $this->assertCount(3, $keyList);
    }
    
    public function testCallsWithoutName()
    {
        $keyring = new Keyring();
        try{
            $keyring->delete();
            $this->assertTrue(false,'Able to delete keyring without name.');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        try{
            $keyring->getKeyList();
            $this->assertTrue(false,'Able to getKeyList keyring without name.');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        try{
            $keyring->linkKey();
            $this->assertTrue(false,'Able to linkKey keyring without name.');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        try{
            $keyring->unLinkKey();
            $this->assertTrue(false,'Able to unLinkKey keyring without name.');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        try{
            $keyring->getStats();
            $this->assertTrue(false,'Able to getStats keyring without name.');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertFalse($keyring->isValid());
    }
}
