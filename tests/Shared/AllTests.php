<?php

namespace ApiAxleTest\Shared;

require_once 'ConfigTest.php';
require_once 'AxleTest.php';
require_once 'ApiExceptionTest.php';

class Shared_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle Shared');
        
        $suite->addTestSuite('\\ApiAxleTest\\Shared\\ConfigTest');
        $suite->addTestSuite('\\ApiAxleTest\\Shared\\AxleTest');
        $suite->addTestSuite('\\ApiAxleTest\\Shared\\ApiExceptionTests');
        
        return $suite;
        
    }
}