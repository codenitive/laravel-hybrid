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
	 * Test memory is properly sync during construct.
	 *
	 * @test
	 */
	public function testMemoryIsProperlySync()
	{
		$acl  = $this->stub;
		$refl = new \ReflectionObject($acl);

		$prop_memory = $refl->getProperty('memory');
		$prop_memory->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Memory\Runtime', $prop_memory->getValue($acl));

		$prop_roles = $refl->getProperty('roles');
		$prop_roles->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Acl\Fluent', $prop_roles->getValue($acl));

		$this->assertTrue($acl->roles()->has('guest'));
		$this->assertTrue($acl->roles()->has('admin'));
		$this->assertTrue($acl->has_role('guest'));
		$this->assertTrue($acl->has_role('admin'));

		$prop_actions = $refl->getProperty('actions');
		$prop_actions->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Acl\Fluent', $prop_actions->getValue($acl));

		$this->assertTrue($acl->actions()->has('manage-user'));
		$this->assertTrue($acl->actions()->has('manage'));
		$this->assertTrue($acl->has_action('manage-user'));
		$this->assertTrue($acl->has_action('manage'));
	}
}