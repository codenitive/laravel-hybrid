<?php

Bundle::start('hybrid');

class MemoryDriverTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test Hybrid\Memory\Driver::initiate()
	 *
	 * @test
	 */
	public function testInitiateMethod()
	{
		$stub = new MemoryDriverStub;
		$this->assertTrue($stub->initiated);
	}

	/**
	 * Test Hybrid\Memory\Driver::shutdown()
	 *
	 * @test
	 */
	public function testShutdownMethod()
	{
		$stub = new MemoryDriverStub;
		$this->assertFalse($stub->shutdown);
		$stub->shutdown();
		$this->assertTrue($stub->shutdown);
	}

	/**
	 * Test Hybrid\Memory\Driver::stringify()
	 *
	 * @test
	 */
	public function testStringifyMethod()
	{
		$stub     = new MemoryDriverStub;
		$expected = 'a:2:{s:4:"name";s:9:"Orchestra";s:5:"theme";a:2:{s:7:"backend";s:7:"default";s:8:"frontend";s:7:"default";}}';
		$stream   = fopen(Bundle::path('hybrid').'tests'.DS.'memory'.DS.'driver.stub.php', 'r');
		$output   = $stub->stringify($stream);

		$this->assertEquals($expected, $output);

		$expected = array(
			'name'  => 'Orchestra',
			'theme' => array(
				'backend' => 'default',
				'frontend' => 'default',
			),
		);

		$this->assertEquals($expected, unserialize($output));
	}
}

class MemoryDriverStub extends Hybrid\Memory\Driver {

	public $initiated = false;
	public $shutdown  = false;

	public function initiate() 
	{
		$this->initiated = true;
	}

	public function shutdown() 
	{
		$this->shutdown = true;
	}
}