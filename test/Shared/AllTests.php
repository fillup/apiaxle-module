<?php

namespace ApiAxle\Test\Shared;

require_once 'ConfigTest.php';


class Shared_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('ApiAxle Shared');
        
        $suite->addTestSuite('\\ApiAxle\\Test\\Shared\\ConfigTest');
        
        return $suite;
        
    }
}