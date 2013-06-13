<?php
namespace ApiAxle\Test\Api;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\Config;
use ApiAxle\Api\Api;
use ApiAxle\Shared\ApiException;

class ApiTest extends \PHPUnit_Framework_TestCase
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
            $api->delete($apiName);
            $apiList = $api->getList();
            $this->assertArrayNotHasKey($apiName, $apiList->getItemsArray());
        } catch(ApiException $ae){
            echo $ae;
        } catch(\Exception $e){
            echo $e;
        }
    }
    

}