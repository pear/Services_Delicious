<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Services_Delicious_AllTests::main');
}

require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Services_DeliciousTest.php';

class Services_Delicious_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PEAR - Services_Delicious');

        $suite->addTestSuite('Services_DeliciousTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Services_Delicious_AllTests::main') {
    Services_Delicious_AllTests::main();
}