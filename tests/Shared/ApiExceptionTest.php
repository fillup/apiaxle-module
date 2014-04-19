<?php
namespace ApiAxleTest\Shared;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\ApiException;

class ApiExceptionTests extends \PHPUnit_Framework_TestCase
{
    public function testApiExceptionConstructor()
    {
        $apiException = new ApiException('This is the message', 101, null, 403, 'the response');
        $this->assertEquals('This is the message',$apiException->getMessage());
        $this->assertEquals(101,$apiException->getCode());
        $this->assertEquals(403,$apiException->getHttpCode());
        $this->assertEquals('the response',$apiException->getResponse());
    }
    
    public function testResponseArray()
    {
        $response = array('attr'=>'value');
        $apiException = new ApiException('This is the message', 101, null, 403, $response);
        $this->assertEquals(json_encode($response),$apiException->getResponse());
    }
    
    public function testToString()
    {
        $apiException = new ApiException('This is the message', 101, null, 403, 'the response');
        $this->assertTrue(is_string($apiException->__toString()));
    }
}