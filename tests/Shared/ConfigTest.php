<?php
namespace ApiAxleTest\Shared;

require_once __DIR__.'/../../vendor/autoload.php';

use ApiAxle\Shared\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testInitializeWithArray()
    {
        $configArray = array(
            'endpoint' => 'https://apiaxle.api.local/v1/',
            'key' => 'asf2oijwoifj4ofj2of42f2',
            'secret' => '24j2oi4jfo3in4foij3oijfaoij3oij4f3oifjf'
        );
        
        $config = new Config($configArray);
        $this->assertEquals($configArray['endpoint'], $config->getEndpoint());
        $this->assertEquals($configArray['key'], $config->getKey());
        $this->assertEquals($configArray['secret'], $config->getSecret());
    }
    
    public function testInitializeWithFile()
    {
        $configArray = include __DIR__.'/../../config/config.local.php';

        $config = new Config();
        $this->assertEquals($configArray['endpoint'], $config->getEndpoint());
        $this->assertEquals($configArray['key'], $config->getKey());
        $this->assertEquals($configArray['secret'], $config->getSecret());
    }
}