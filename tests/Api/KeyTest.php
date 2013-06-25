<?php
namespace ApiAxleTest\Api;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\Config;
use ApiAxle\Api\Key;
use ApiAxle\Api\Api;
use ApiAxle\Shared\ApiException;

class KeyTests extends \PHPUnit_Framework_TestCase
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
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
    }
    
    public function testCreateKey()
    {
        $keyValue = 'test-'.str_replace(array(' ','.'),'',microtime());
        $key = new Key();
        $key->create($keyValue);
        $this->assertEquals($keyValue, $key->getKey());
    }
    
    public function testCreateUpdateKey()
    {
        $keyValue = 'test-'.str_replace(array(' ','.'),'',microtime());
        $key = new Key();
        $createData = array(
            'sharedSecret' => 'firstsecret'
        );
        $key->create($keyValue,$createData);
        $updateData = array(
            'sharedSecret' => 'updatedsecret'
        );
        $key->update($updateData);
        $keyData = $key->getData();
        $this->assertEquals($updateData['sharedSecret'], $keyData['sharedSecret']);
    }
    
    public function testCreateDeleteKey()
    {
        $keyValue = 'test-'.str_replace(array(' ','.'),'',microtime());
        $key = new Key();
        $key->create($keyValue);
        $keyList = $key->getList();
        $hasKey = false;
        foreach($keyList as $item){
            if($item->getKey() == $keyValue){
                $hasKey = true;
                break;
            }
        }
        $this->assertTrue($hasKey,'Created key not found in list from server');
        $key->delete($keyValue);
        $keyList = $key->getList();
        $hasKey = false;
        foreach($keyList as $item){
            if($item->getKey() == $keyValue){
                $hasKey = true;
                break;
            }
        }
        $this->assertFalse($hasKey,'Created key still exists after deletion.');
    }
    
    public function testListKeys()
    {
        $key = new Key();
        $keyList = $key->getList();
        $this->assertInstanceOf('ApiAxle\Shared\ItemList', $keyList);
    }
    
    public function testGetApiList()
    {
        $config = new Config();
        $key = new Key();
        $key->setKey($config->getKey());
        $apiList = $key->getApiList();
        //print_r($apiList);
        $this->assertInstanceOf('ApiAxle\Shared\ItemList', $apiList);
    }
    
    public function testGetApiCharts()
    {
        $config = new Config();
        $key = new Key();
        $key->setKey($config->getKey());
        $apiCharts = $key->getApiCharts();
        //print_r($apiCharts);
        $this->assertInstanceOf('\stdClass', $apiCharts);
    }
    
    public function testGetStatsForAllApis()
    {
        $config = new Config();
        $key = new Key();
        $key->setKey($config->getKey());
        $apiStats = $key->getStats();
        //print_r($apiStats);
        $this->assertInstanceOf('\stdClass', $apiStats);
    }
    
    public function testGetStatsForSpecificApi()
    {
        $config = new Config();
        $key = new Key();
        $key->setKey($config->getKey());
        $apiStats = $key->getStats(false,false,'minute',true,'epoch_seconds','apiaxle');
        //print_r($apiStats);
        $this->assertInstanceOf('\stdClass', $apiStats);
        $api = new Api();
        $api->setName('apiaxle');
        $apiStats = $key->getStats(false,false,'minute',true,'epoch_seconds',$api);
        //print_r($apiStats);
        $this->assertInstanceOf('\stdClass', $apiStats);
    }
    
    public function testGetCharts()
    {
        $key = new Key();
        $charts = $key->getCharts();
        //print_r($charts);
        $this->assertInstanceOf('\stdClass', $charts);
    }
}