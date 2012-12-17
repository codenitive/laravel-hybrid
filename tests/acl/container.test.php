<?php

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
		$runtime = Hybrid\Memory::make('runtime.foo');

		$this->stub->add_role('foo');
		$this->stub->add_action('foobar');
		$this->stub->allow('foo', 'foobar');

		$refl    = new \ReflectionObject($this->stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');
		$acl     = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$roles->getValue($this->stub)->get());
		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$memory->getValue($this->stub)->get('acl_foo.roles'));
		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$runtime->get('acl_foo.roles'));

		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$actions->getValue($this->stub)->get());
		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$memory->getValue($this->stub)->get('acl_foo.actions'));
		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$runtime->get('acl_foo.actions'));

		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true), 
			$acl->getValue($this->stub));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true), 
			$memory->getValue($this->stub)->get('acl_foo.acl'));
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
		$this->markTestIncomplete('incompleted');
	}

	/**
	 * Test Hybrid\Acl\Container::deny() method.
	 *
	 * @test
	 */
	public function testDenyMethod()
	{
		$this->markTestIncomplete('incompleted');
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
		$runtime = new Hybrid\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory()); 

		$acl     = new Hybrid\Acl\Container('foo', $runtime);
		$refl    = new \ReflectionObject($acl);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Memory\Runtime', $memory->getValue($acl));

		$this->assertInstanceOf('Hybrid\Acl\Fluent', $roles->getValue($acl));

		$this->assertTrue($acl->roles()->has('guest'));
		$this->assertTrue($acl->roles()->has('admin'));
		$this->assertTrue($acl->has_role('guest'));
		$this->assertTrue($acl->has_role('admin'));
		$this->assertEquals(array('guest', 'admin'), $roles->getValue($acl)->get());
		$this->assertEquals(array('guest', 'admin'), $acl->roles()->get());

		$this->assertInstanceOf('Hybrid\Acl\Fluent', $actions->getValue($acl));

		$this->assertTrue($acl->actions()->has('manage-user'));
		$this->assertTrue($acl->actions()->has('manage'));
		$this->assertTrue($acl->has_action('manage-user'));
		$this->assertTrue($acl->has_action('manage'));
		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($acl)->get());
		$this->assertEquals(array('manage-user', 'manage'), $acl->actions()->get());
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

		$acl     = new Hybrid\Acl\Container('foo', $runtime);
		$refl    = new \ReflectionObject($acl);
		$actions = $refl->getProperty('actions');
		$roles   = $refl->getProperty('roles');

		$actions->setAccessible(true);
		$roles->setAccessible(true);

		$acl->roles()->add('admin');
		$acl->roles()->fill(array('admin'));
		$acl->add_role('admin');
		$acl->add_roles(array('admin'));

		$acl->actions()->add('manage');
		$acl->actions()->fill(array('manage'));
		$acl->add_action('manage');
		$acl->add_actions(array('manage'));

		$this->assertEquals(array('guest', 'admin'), $roles->getValue($acl)->get());
		$this->assertEquals(array('guest', 'admin'), $acl->roles()->get());

		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($acl)->get());
		$this->assertEquals(array('manage-user', 'manage'), $acl->actions()->get());
	}
}