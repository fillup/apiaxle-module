<?php

namespace ApiAxle\Test\Api;

require_once 'ApiTest.php';

class Api_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle Api');
        
        $suite->addTestSuite('\\ApiAxle\\Test\\Api\\ApiTest');
        
        return $suite;
        
    }
}