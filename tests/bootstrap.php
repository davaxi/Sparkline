<?php

use PHPUnit\Framework\TestCase;

error_reporting(E_ALL ^ E_DEPRECATED);

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Paris');

class SparklinePHPUnit extends TestCase
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

