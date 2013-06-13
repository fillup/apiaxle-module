<?php
namespace ApiAxle\Test\Api;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\Config;
use ApiAxle\Api\Key;
use ApiAxle\Shared\ApiException;

class KeyTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateKey()
    {
        $keyValue = 'test-'.str_replace(array(' ','.'),'',microtime());
        $key = new Key();
        $key->create($keyValue);
        $this->assertEquals($keyValue, $key->getKey());
    }
}