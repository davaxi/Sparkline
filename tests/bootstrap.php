<?php

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Paris');

if (class_exists('PHPUnit_Framework_TestCase')) {
    class SparklinePHPUnit extends PHPUnit_Framework_TestCase
    {
    }
}
else {
    class SparklinePHPUnit extends \PHPUnit\Framework\TestCase
    {
    }
}