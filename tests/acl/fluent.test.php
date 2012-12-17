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
	 * Test add a single key.
	 *
	 * @test
	 */
	public function testAddKey()
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
	 * Test add multiple key.
	 *
	 * @test
	 */
	public function testAddMulipleKey()
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
	 * Test add a single key.
	 *
	 * @test
	 */
	public function testHasKey()
	{
		$this->assertTrue($this->stub->has('hello-world'));
		$this->assertFalse($this->stub->has('goodbye-world'));
	}

	/**
	 * Test rename key.
	 *
	 * @test
	 */
	public function testRenameKey()
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
	 * Test search key.
	 *
	 * @test
	 */
	public function testSearchKey()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$this->assertEquals(0, $stub->search('foo'));
		$this->assertEquals(1, $stub->search('foobar'));
		$this->assertTrue(is_null($stub->search('laravel')));
	}

	/**
	 * Test exist key.
	 *
	 * @test
	 */
	public function testExistKey()
	{
		$stub = new Hybrid\Acl\Fluent('foo');

		$stub->fill(array('foo', 'foobar'));

		$this->assertTrue($stub->exist(0));
		$this->assertTrue($stub->exist(1));
		$this->assertFalse($stub->exist(3));
	}

	/**
	 * Test remove key.
	 *
	 * @test
	 */
	public function testRemoveKey()
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
}