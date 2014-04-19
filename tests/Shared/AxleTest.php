<?php
namespace ApiAxleTest\Shared;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\Config;
use ApiAxle\Shared\ApiException;
use ApiAxle\Api\Api;

/**
 * Tests specific to connecting to ApiAxle
 */
class AxleTest extends \PHPUnit_Framework_TestCase
{
    public function testServerUnavailable()
    {
        $configArray = array(
            'endpoint' => 'https://doesntexist',
            'key' => 'asf2oijwoifj4ofj2of42f2',
            'secret' => '24j2oi4jfo3in4foij3oijfaoij3oij4f3oifjf'
        );
        
        $config = new Config($configArray);
        try{
            $api = new Api($config,'apiaxle');
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertEquals(210,$e->getCode());
        }
    }
    
    public function testInvalidKey()
    {
        $config = new Config();
        $config->setKey('invalid');
        try{
            $api = new Api($config,'apiaxle');
            $this->assertTrue(false);
        } catch (ApiException $e) {
            $this->assertEquals(403,$e->getHttpCode());
        }
    }
    
    public function testInvalidSecret()
    {
        $config = new Config();
        $config->setSecret('invalid');
        try{
            $api = new Api($config,'apiaxle');
            $this->assertTrue(false);
        } catch (ApiException $e) {
            $this->assertEquals(403,$e->getHttpCode());
        }
    }
}