<?php

Bundle::start('hybrid');

class MemoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$mock = Hybrid\Memory::make('runtime.mock');

		$mock->put('foo.bar', 'hello world');
		
		$mock->put('username', 'laravel');

		$mock = Hybrid\Memory::make('cache.mock');

		$mock->put('foo.bar', 'hello world');
		
		$mock->put('foobar', function ()
		{
			return 'hello world foobar';
		});
		
		$mock->get('hello.world', function () use ($mock)
		{
			return $mock->put('hello.world', 'HELLO WORLD');
		});

		$mock->put('username', 'laravel');

		Hybrid\Memory::extend('stub', function($driver, $config) 
		{
			return new MemoryStub($driver, $config);
		});
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
	 * Test Hybrid\Memory::start() method.
	 *
	 * @test
	 */
	public function testStartMethod()
	{
		$this->assertTrue(Hybrid\Memory::start());
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
	public function testCacheMock()
	{
		$mock = Hybrid\Memory::make('cache.mock');
		
		$this->assertEquals(array('bar' => 'hello world'), $mock->get('foo'));
		$this->assertEquals('hello world', $mock->get('foo.bar'));
		$this->assertEquals('laravel', $mock->get('username'));
	}

	/**
	 * Test that Hybrid\Memory return valid values
	 *
	 * @test
	 */
	public function testCacheMockWithClosure()
	{
		$mock = Hybrid\Memory::make('cache.mock');
		
		$this->assertEquals('hello world foobar', $mock->get('foobar'));
		$this->assertEquals('HELLO WORLD', $mock->get('hello.world'));
	}

	/**
	 * Test Hybrid\Memory::extend() return valid Memory instance.
	 *
	 * @test
	 */
	public function testStubMemory()
	{
		$stub = Hybrid\Memory::make('stub.mock');

		$this->assertInstanceOf('MemoryStub', $stub);

		$refl    = new \ReflectionObject($stub);
		$storage = $refl->getProperty('storage');
		$storage->setAccessible(true);

		$this->assertEquals('stub', $storage->getValue($stub));
	}

	/**
	 * Test Hybrid\Memory::__construct() method.
	 *
	 * @expectedException Hybrid\RuntimeException
	 */
	public function testConstructMethod()
	{
		$stub = new Hybrid\Memory;
	}

}

class MemoryStub extends Hybrid\Memory\Driver
{
	/**
	 * Storage name
	 * 
	 * @access  protected
	 * @var     string  
	 */
	protected $storage = 'stub';

	/**
	 * No initialize method for runtime
	 *
	 * @access  public
	 * @return  void
	 */
	public function initiate() {}

	/**
	 * No shutdown method for runtime
	 *
	 * @access  public
	 * @return  void
	 */
	public function shutdown() {}
}