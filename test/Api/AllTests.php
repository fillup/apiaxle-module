<?php

namespace ApiAxle\Test\Api;

require_once 'ApiTest.php';
require_once 'KeyTest.php';

class Api_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle Api');
        
        $suite->addTestSuite('\\ApiAxle\\Test\\Api\\ApiTest');
        $suite->addTestSuite('\\ApiAxle\\Test\\Api\\KeyTest');
        
        return $suite;
        
    }
}