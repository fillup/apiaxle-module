<?php
namespace ApiAxle\Test\Api;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\Config;
use ApiAxle\Api\Api;

class ApiTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigAutoInitialized()
    {
        $api = new Api();
        $this->assertInstanceOf('ApiAxle\Shared\Config', $api->getConfig(),
                'Returned configuration is not of type ApiAxle\Shared\Config');
    }
    
    public function testListApis()
    {
        $api = new Api();
        $apiList = $api->getList();
        print_r($apiList);
        //$this->assertInstanceOf('ApiAxle\Shared\ItemList', $apiList);
        
    }
}