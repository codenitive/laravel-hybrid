<?php

Bundle::start('hybrid');

class MemoryDriverTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test Hybrid\Memory\Driver::stringify()
	 *
	 * @test
	 */
	public function testStringify()
	{
		$stub = new MemoryDriverStub;

		$expected =  array(
			'default_role' => 1, 
			'member_role'  => 2,
		);
		
		$data = 'a:2:{s:12:"default_role";i:1;s:11:"member_role";i:2;}';

		$this->assertEquals($expected, $stub->test_stringify($data));
	}
}

class MemoryDriverStub extends Hybrid\Memory\Driver {

	public $initiated = false;
	public $shutdown  = false;

	public function test_stringify($data)
	{
		return $this->stringify($data);
	}

	public function initiate() 
	{
		$this->initiated = true;
	}

	public function shutdown() 
	{
		$this->shutdown = true;
	}
}