<?php

namespace ApiAxle\Tests\Api;

require_once 'ApiTest.php';
require_once 'KeyTest.php';

class Api_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle Api');
        
        $suite->addTestSuite('\\ApiAxle\\Tests\\Api\\ApiTests');
        $suite->addTestSuite('\\ApiAxle\\Tests\\Api\\KeyTests');
        
        return $suite;
        
    }
}