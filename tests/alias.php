<?php
/**
 * This file allows PHPUnit TestCases for PHPUnit version 6 to be run under PHPUnit version 5 by aliasing the classes
 * with namespaces to the old ones without to allow tests to be run under PHP 5
 */
if (!class_exists('\PHPUnit\Framework\TestCase') && class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}