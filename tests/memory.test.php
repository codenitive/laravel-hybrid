<?php

class TestMemory extends PHPUnit_Framework_TestCase 
{
	/**
	 * Setup: Start Hybrid Bundle
	 *
	 * @return  void
	 */
	public function setup()
	{
		Bundle::start('hybrid');

		$mock = Hybrid\Memory::make('runtime.mock');

		$mock->put('foo.bar', 'hello world');
		$mock->put('username', 'laravel');

		$mock = Hybrid\Memory::make('cache.mock');

		$mock->put('foo.bar', 'hello world');
		$mock->put('username', 'laravel');
	}

	/**
	 * Test that Hybrid\Memory::make() return an instanceof Hybrid\Memory.
	 * 
	 * @test
	 * @return  void
	 */
	public function testMake()
	{
		$this->assertInstanceOf('Hybrid\Memory\Runtime', Hybrid\Memory::make('runtime')); 
		$this->assertInstanceOf('Hybrid\Memory\Cache', Hybrid\Memory::make('cache')); 
	}

	/**
	 * Test that Hybrid\Memory::make() return exception when given invalid driver
	 *
	 * @test
	 * @expectedException Hybrid\Exception
	 */
	public function testMakeExpectedException()
	{
		Hybrid\Memory::make('orm');
	}

	/**
	 * Test that Hybrid\Memory return valid values
	 *
	 * @test
	 */
	public function testGetRuntimeMock()
	{
		$mock = Hybrid\Memory::make('runtime.mock');
		
		$this->assertEquals(array('bar' => 'hello world'), $mock->get('foo'));
		$this->assertEquals('hello world', $mock->get('foo.bar'));
		$this->assertEquals('laravel', $mock->get('username'));
	}

	/**
	 * Test that Hybrid\Memory return valid values
	 *
	 * @test
	 */
	public function testCacheCacheMock()
	{
		$mock = Hybrid\Memory::make('cache.mock');
		
		$this->assertEquals(array('bar' => 'hello world'), $mock->get('foo'));
		$this->assertEquals('hello world', $mock->get('foo.bar'));
		$this->assertEquals('laravel', $mock->get('username'));
	}
}