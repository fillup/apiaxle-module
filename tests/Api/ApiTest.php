<?php
namespace ApiAxle\Tests\Api;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\Config;
use ApiAxle\Api\Api;
use ApiAxle\Api\Key;
use ApiAxle\Shared\ApiException;

class ApiTests extends \PHPUnit_Framework_TestCase
{
    
    public static function tearDownAfterClass()
    {
        try {
            $api = new Api();
            $apiList = $api->getList();
            foreach($apiList as $item){
                if(strpos($item->getName(),'test-') !== false){
                    $api->delete($item->getName());
                }
            }
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
    }
    
    public function testConfigAutoInitialized()
    {
        $api = new Api();
        $this->assertInstanceOf('ApiAxle\Shared\Config', $api->getConfig(),
                'Returned configuration is not of type ApiAxle\Shared\Config');
    }
    
    public function testListApis()
    {
        $api = new Api();
        try{
            $apiList = $api->getList();
            //print_r($apiList);
            $this->assertInstanceOf('ApiAxle\Shared\ItemList', $apiList);
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
        
    }
    
    public function testGetApi()
    {
        $apiName = 'apiaxle';
        $api = new Api();
        $api->get($apiName);
        //print_r($api);
        $this->assertEquals($apiName, $api->getName());
    }
    
    public function testCreateApi()
    {
        $apiName = 'test-'.str_replace(array(' ','.'),'',microtime());
        $data = array(
            'endPoint' => 'localhost'
        );
        $api = new Api();
        try{
            $api->create($apiName, $data);
            //print_r($api);
            $this->assertEquals($apiName,$api->getName());
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
    }
    
    public function testCreateUpdateApi()
    {
        $apiName = 'test-'.str_replace(array(' ','.'),'',microtime());
        $data = array(
            'endPoint' => 'localhost'
        );
        $api = new Api();
        try{
            $api->create($apiName, $data);
            $data = array(
                'endPoint' => 'differenthost'
            );
            $api->update($data);
            $apiData = $api->getData();
            $this->assertRegExp('/[0-9]{1,}/',(string)$apiData['updatedAt']);
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
    }
    
    public function testCreateDeleteApi()
    {
        $apiName = 'test-'.str_replace(array(' ','.'),'',microtime());
        $data = array(
            'endPoint' => 'localhost'
        );
        $api = new Api();
        try{
            $api->create($apiName, $data);
            $apiList = $api->getList();
            $hasApi = false;
            foreach($apiList as $item){
                if($item->getName() == $apiName){
                    $hasApi = true;
                    break;
                }
            }
            $this->assertTrue($hasApi,'Created api not found in list from server');
            $api->delete($apiName);
            $apiList = $api->getList();
            $hasApi = false;
            foreach($apiList as $item){
                if($item->getName() == $apiName){
                    $hasApi = true;
                    break;
                }
            }
            $this->assertFalse($hasApi,'Created API still exists after deletion.');
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
    }
    
    public function testGetKeyCharts()
    {
        $apiName = 'apiaxle';
        $api = new Api();
        $api->setName($apiName);
        $keycharts = $api->getKeyCharts();
        //print_r($keycharts);
        $this->assertInstanceOf('\stdClass', $keycharts);
    }
    
    public function testGetKeyList()
    {
        $apiName = 'apiaxle';
        $api = new Api();
        $api->setName($apiName);
        $keyList = $api->getKeyList(0,100,'true');
        //print_r($keyList);
        $this->assertInstanceOf('\ApiAxle\Shared\ItemList', $keyList);
    }
    
    public function testLinkUnlinkKey()
    {
        $apiName = 'test-'.str_replace(array(' ','.'),'',microtime());
        $data = array(
            'endPoint' => 'localhost'
        );
        $api = new Api();
        $api->create($apiName, $data);
        $key = new Key();
        $key->create($apiName);
        $api->linkKey($key);
        $hasAssoc = false;
        $apiList = $key->getApiList();
        foreach($apiList as $item){
            if($item->getName() == $apiName){
                $hasAssoc = true;
            }
        }
        $this->assertTrue($hasAssoc,'New key is not linked to new API');
        $api->unLinkKey($key);
        $hasAssoc = false;
        $apiList = $key->getApiList();
        foreach($apiList as $item){
            if($item->getName() == $apiName){
                $hasAssoc = true;
            }
        }
        $this->assertFalse($hasAssoc,'New key is still linked to new API');
    }
    
    public function testGetStats()
    {
        $api = new API();
        $api->setName('apiaxle');
        $apiStats = $api->getStats();
        //print_r($apiStats);
        $this->assertInstanceOf('\stdClass', $apiStats);
    }
    
    public function testGetCharts()
    {
        $api = new Api();
        $charts = $api->getCharts('day');
        //print_r($charts);
        $this->assertInstanceOf('\stdClass', $charts);
    }

}