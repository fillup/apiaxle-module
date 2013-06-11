<?php

namespace ApiAxle\Tests;

require_once 'Shared/AllTests.php';
require_once 'Api/AllTests.php';


class AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle');
        
        $suite->addTest(\ApiAxle\Test\Shared\Shared_AllTests::suite());
        $suite->addTest(\ApiAxle\Test\Api\Api_AllTests::suite());
        
        return $suite;
    }
}