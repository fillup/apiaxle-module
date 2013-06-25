<?php

namespace ApiAxleTest\Api;

require_once 'ApiTest.php';
require_once 'KeyTest.php';
require_once 'KeyringTest.php';

class Api_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle Api');
        
        $suite->addTestSuite('\\ApiAxleTest\\Api\\ApiTests');
        $suite->addTestSuite('\\ApiAxleTest\\Api\\KeyTests');
        $suite->addTestSuite('\\ApiAxleTest\\Api\\KeyringTests');
        
        return $suite;
        
    }
}