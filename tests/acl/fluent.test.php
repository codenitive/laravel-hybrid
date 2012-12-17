<?php

Bundle::start('hybrid');

class AclFluentTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Stub instance.
	 * 
	 * @return Hybrid\Acl\Fluent
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = new Hybrid\Acl\Fluent('stub');
		$this->stub->fill(array(
			'Hello World'
		));
	}

	/**
	 * Test instanceof stub.
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$this->assertInstanceOf('Hybrid\Acl\Fluent', $this->stub);

		$refl = new \ReflectionObject($this->stub);
		$name = $refl->getProperty('name');
		$name->setAccessible(true);

		$this->assertEquals('stub', $name->getValue($this->stub));
	}

	/**
	 * Test Hybrid\Acl\Fluent::add() method.
	 *
	 * @test
	 */
	public function testAddMethod()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->add('foo');
		$stub->add('foobar');

		$refl = new \ReflectionObject($stub);
		$collections = $refl->getProperty('collections');
		$collections->setAccessible(true);

		$this->assertEquals(array('foo', 'foobar'), $collections->getValue($stub));
		$this->assertEquals(array('foo', 'foobar'), $stub->get());
	}

	/**
	 * Test Hybrid\Acl\Fluent::add() method null throw an exception.
	 *
	 * @test
	 * @expectedException Hybrid\InvalidArgumentException
	 */
	public function testAddMethodNullThrownException()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->add(null);
	}

	/**
	 * Test Hybrid\Acl\Fluent::fill() method.
	 *
	 * @test
	 */
	public function testFillMethod()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$refl = new \ReflectionObject($stub);
		$collections = $refl->getProperty('collections');
		$collections->setAccessible(true);

		$this->assertEquals(array('foo', 'foobar'), $collections->getValue($stub));
		$this->assertEquals(array('foo', 'foobar'), $stub->get());
	}

	/**
	 * Test Hybrid\Acl\Fluent::add() method null throw an exception.
	 *
	 * @test
	 * @expectedException Hybrid\InvalidArgumentException
	 */
	public function testFillMethodNullThrownException()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->fill(array(null));
	}

	/**
	 * Test Hybrid\Acl\Fluent::has() method.
	 *
	 * @test
	 */
	public function testHasMethod()
	{
		$this->assertTrue($this->stub->has('hello-world'));
		$this->assertFalse($this->stub->has('goodbye-world'));
	}

	/**
	 * Test Hybrid\Acl\Fluent::rename() method.
	 *
	 * @test
	 */
	public function testRenameMethod()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$stub->rename('foo', 'laravel');

		$refl = new \ReflectionObject($stub);
		$collections = $refl->getProperty('collections');
		$collections->setAccessible(true);

		$this->assertEquals(array('laravel', 'foobar'), $collections->getValue($stub));
		$this->assertEquals(array('laravel', 'foobar'), $stub->get());
	}

	/**
	 * Test Hybrid\Acl\Fluent::search() method.
	 *
	 * @test
	 */
	public function testSearchMethod()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$this->assertEquals(0, $stub->search('foo'));
		$this->assertEquals(1, $stub->search('foobar'));
		$this->assertTrue(is_null($stub->search('laravel')));
	}

	/**
	 * Test Hybrid\Acl\Fluent::exist() method.
	 *
	 * @test
	 */
	public function testExistMethod()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$this->assertTrue($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertFalse($stub->exist(3));
	}

	/**
	 * Test Hybrid\Acl\Fluent::remove() method.
	 *
	 * @test
	 */
	public function testRemoveMethod()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$this->assertEquals(array('foo', 'foobar'), $stub->get());

		$stub->remove('foo');

		$this->assertFalse($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertEquals(array(1 => 'foobar'), $stub->get());

		$stub->fill(array('foo'));

		$this->assertEquals(array(1 => 'foobar', 2 => 'foo'), $stub->get());

		$stub->remove('foo');

		$this->assertFalse($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertFalse($stub->exist(2));
		$this->assertEquals(array(1 => 'foobar'), $stub->get());
	}

	/**
	 * Test Hybrid\Acl\Fluent::remove() method null throw an exception.
	 *
	 * @test
	 * @expectedException Hybrid\InvalidArgumentException
	 */
	public function testRemoveMethodNullThrownException()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->remove(null);
	}
}