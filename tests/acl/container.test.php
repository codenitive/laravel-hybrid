<?php

Bundle::start('hybrid');

class AclContainerTest extends PHPUnit_Framework_TestCase {

	/**
	 * Acl Container instance.
	 *
	 * @var Hybrid\Acl\Container
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$runtime = Hybrid\Memory::make('runtime.foo');
		$runtime->put('acl_foo', static::providerMemory());

		$this->stub = Hybrid\Acl::make('foo', $runtime);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Add data provider
	 * 
	 * @return array
	 */
	public static function providerMemory()
	{
		return array(
			'acl'     => array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true),
			'actions' => array('manage-user', 'manage'),
			'roles'   => array('guest', 'admin'),
		);
	}

	/**
	 * Test instance of stub.
	 *
	 * @test
	 */
	public function testInstanceOfStub()
	{
		$this->assertInstanceOf('Hybrid\Acl\Container', $this->stub);
	}

	/**
	 * Test sync memory.
	 *
	 * @test
	 */
	public function testSyncMemoryAfterConstruct()
	{
		$runtime = new Hybrid\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub    = new Hybrid\Acl\Container('foo', $runtime);

		$stub->add_role('foo');
		$stub->add_action('foobar');
		$stub->allow('foo', 'foobar');

		$refl    = new \ReflectionObject($stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');
		$acl     = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$roles->getValue($stub)->get());
		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$memory->getValue($stub)->get('acl_foo.roles'));
		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$runtime->get('acl_foo.roles'));

		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$actions->getValue($stub)->get());
		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$memory->getValue($stub)->get('acl_foo.actions'));
		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$runtime->get('acl_foo.actions'));

		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true), 
			$acl->getValue($stub));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true), 
			$memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true), 
			$runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Hybrid\Acl\Container::allow() method.
	 *
	 * @test
	 */
	public function testAllowMethod()
	{
		$runtime = new Hybrid\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub    = new Hybrid\Acl\Container('foo', $runtime);

		$stub->allow('guest', 'manage-user');

		$refl   = new \ReflectionObject($this->stub);
		$memory = $refl->getProperty('memory');
		$acl    = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertEquals(array('0:0' => true, '0:1' => false, '1:0' => true, '1:1' => true), 
			$acl->getValue($stub));
		$this->assertEquals(array('0:0' => true, '0:1' => false, '1:0' => true, '1:1' => true), 
			$memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals(array('0:0' => true, '0:1' => false, '1:0' => true, '1:1' => true), 
			$runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Hybrid\Acl\Container::deny() method.
	 *
	 * @test
	 */
	public function testDenyMethod()
	{
		$runtime = new Hybrid\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub    = new Hybrid\Acl\Container('foo', $runtime);

		$stub->deny('admin', 'manage-user');

		$refl   = new \ReflectionObject($this->stub);
		$memory = $refl->getProperty('memory');
		$acl    = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => false, '1:1' => true), 
			$acl->getValue($stub));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => false, '1:1' => true), 
			$memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => false, '1:1' => true), 
			$runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Hybrid\Acl\Container::can() method.
	 *
	 * @test
	 */
	public function testCanMethod()
	{
		$this->markTestIncomplete('incompleted');
	}

	/**
	 * Test memory is properly sync during construct.
	 *
	 * @test
	 */
	public function testMemoryIsProperlySync()
	{
		$stub    = $this->stub;
		$refl    = new \ReflectionObject($stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Memory\Runtime', $memory->getValue($stub));

		$this->assertInstanceOf('Hybrid\Acl\Fluent', $roles->getValue($stub));

		$this->assertTrue($stub->roles()->has('guest'));
		$this->assertTrue($stub->roles()->has('admin'));
		$this->assertTrue($stub->has_role('guest'));
		$this->assertTrue($stub->has_role('admin'));
		$this->assertEquals(array('guest', 'admin'), $roles->getValue($stub)->get());
		$this->assertEquals(array('guest', 'admin'), $stub->roles()->get());

		$this->assertInstanceOf('Hybrid\Acl\Fluent', $actions->getValue($stub));

		$this->assertTrue($stub->actions()->has('manage-user'));
		$this->assertTrue($stub->actions()->has('manage'));
		$this->assertTrue($stub->has_action('manage-user'));
		$this->assertTrue($stub->has_action('manage'));
		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($stub)->get());
		$this->assertEquals(array('manage-user', 'manage'), $stub->actions()->get());
	}

	/**
	 * Test adding duplicate roles and actions is properly handled
	 *
	 * @test
	 */
	public function testAddDuplicates()
	{
		$runtime = new Hybrid\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory()); 

		$stub    = new Hybrid\Acl\Container('foo', $runtime);
		$refl    = new \ReflectionObject($stub);
		$actions = $refl->getProperty('actions');
		$roles   = $refl->getProperty('roles');

		$actions->setAccessible(true);
		$roles->setAccessible(true);

		$stub->roles()->add('admin');
		$stub->roles()->fill(array('admin'));
		$stub->add_role('admin');
		$stub->add_roles(array('admin'));

		$stub->actions()->add('manage');
		$stub->actions()->fill(array('manage'));
		$stub->add_action('manage');
		$stub->add_actions(array('manage'));

		$this->assertEquals(array('guest', 'admin'), $roles->getValue($stub)->get());
		$this->assertEquals(array('guest', 'admin'), $stub->roles()->get());

		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($stub)->get());
		$this->assertEquals(array('manage-user', 'manage'), $stub->actions()->get());
	}
}