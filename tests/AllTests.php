<?php

namespace ApiAxleTest;

require_once 'Shared/AllTests.php';
require_once 'Api/AllTests.php';


class AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle');
        
        $suite->addTest(\ApiAxleTest\Shared\Shared_AllTests::suite());
        $suite->addTest(\ApiAxleTest\Api\Api_AllTests::suite());
        
        return $suite;
    }
}