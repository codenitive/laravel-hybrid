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
		$memory = new Hybrid\Memory\Runtime('foo');
		$memory->put('acl_foo', array(
			'acl'     => array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true),
			'actions' => array('manage-user', 'manage'),
			'roles'   => array('guest', 'admin'),
		));
		$this->stub = new Hybrid\Acl\Container('foo', $memory);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
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
		$this->markTestIncomplete('incompleted');
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
		$acl  = $this->stub;
		$refl = new \ReflectionObject($acl);

		$memory = $refl->getProperty('memory');
		$memory->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Memory\Runtime', $memory->getValue($acl));

		$roles = $refl->getProperty('roles');
		$roles->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Acl\Fluent', $roles->getValue($acl));

		$this->assertTrue($acl->roles()->has('guest'));
		$this->assertTrue($acl->roles()->has('admin'));
		$this->assertTrue($acl->has_role('guest'));
		$this->assertTrue($acl->has_role('admin'));
		$this->assertEquals(array('guest', 'admin'), $roles->getValue($this->stub)->get());
		$this->assertEquals(array('guest', 'admin'), $this->stub->roles()->get());

		$actions = $refl->getProperty('actions');
		$actions->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Acl\Fluent', $actions->getValue($acl));

		$this->assertTrue($acl->actions()->has('manage-user'));
		$this->assertTrue($acl->actions()->has('manage'));
		$this->assertTrue($acl->has_action('manage-user'));
		$this->assertTrue($acl->has_action('manage'));
		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($this->stub)->get());
		$this->assertEquals(array('manage-user', 'manage'), $this->stub->actions()->get());
	}

	/**
	 * Test adding duplicate roles and actions is properly handled
	 *
	 * @test
	 */
	public function testAddDuplicates()
	{
		$this->stub->roles()->add('admin');
		$this->stub->roles()->fill(array('admin'));
		$this->stub->add_role('admin');
		$this->stub->add_roles(array('admin'));

		$this->stub->actions()->add('manage');
		$this->stub->actions()->fill(array('manage'));
		$this->stub->add_action('manage');
		$this->stub->add_actions(array('manage'));

		$refl    = new \ReflectionObject($this->stub);
		$actions = $refl->getProperty('actions');
		$roles   = $refl->getProperty('roles');

		$actions->setAccessible(true);
		$roles->setAccessible(true);

		$this->assertEquals(array('guest', 'admin'), $roles->getValue($this->stub)->get());
		$this->assertEquals(array('guest', 'admin'), $this->stub->roles()->get());

		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($this->stub)->get());
		$this->assertEquals(array('manage-user', 'manage'), $this->stub->actions()->get());
	}
}