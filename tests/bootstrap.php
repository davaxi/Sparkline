<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Paris');

if (class_exists('\PHPUnit_Framework_TestCase')) {
    class SparklinePHPUnit extends PHPUnit_Framework_TestCase
    {
        protected function setUp()
        {
            parent::setUp();
            $this->sparkline = new SparklineMockup();
        }

        protected function tearDown()
        {
            parent::tearDown();
            $this->sparkline->destroy();
        }
    }
} else {
    class SparklinePHPUnit extends \PHPUnit\Framework\TestCase
    {
        protected function setUp(): void
        {
            parent::setUp();
            $this->sparkline = new SparklineMockup();
        }

        protected function tearDown(): void
        {
            parent::tearDown();
            $this->sparkline->destroy();
        }
    }
}

