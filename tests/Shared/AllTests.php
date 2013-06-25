<?php

namespace ApiAxleTest\Shared;

require_once 'ConfigTest.php';


class Shared_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle Shared');
        
        $suite->addTestSuite('\\ApiAxleTest\\Shared\\ConfigTest');
        
        return $suite;
        
    }
}