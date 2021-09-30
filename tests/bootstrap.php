<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Paris');

class SparklinePHPUnit extends PHPUnit_Framework_TestCase
{
}
