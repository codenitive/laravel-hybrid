<?php

Bundle::start('hybrid');

class AclTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Hybrid\Acl::register('mock-one', function ($acl)
		{
			$acl->add_actions(array('view blog', 'view forum', 'view news'));
			$acl->allow('guest', array('view blog'));
			$acl->deny('guest', 'view forum');
		});
	}

	/**
	 * Test Hybrid\Acl::make()
	 *
	 * @test
	 */
	public function testMake()
	{
		$this->assertInstanceOf('Hybrid\Acl\Container', Hybrid\Acl::make('mock-one'));
	}

	/**
	 * Test Hybrid\Acl::has_role() given 'mock-one'
	 *
	 * @test
	 */
	public function testHasRoleUsingMockOne()
	{
		$acl = Hybrid\Acl::make('mock-one');
		$this->assertTrue($acl->has_role('Guest'));
		$this->assertFalse($acl->has_role('Adminy'));
	}

	/**
	 * Test Hybrid\Acl::can() given 'mock-one'
	 *
	 * @test
	 */
	public function testCanUsingMockOne()
	{
		$acl    = Hybrid\Acl::make('mock-one');
		$this->assertInstanceOf('Hybrid\Acl\Container', $acl);

		$output = $acl->can('view blog');
		$this->assertTrue($output);
		
		$output = $acl->can('view forum');
		$this->assertFalse($output);

		$output = $acl->can('view news');
		$this->assertFalse($output);
	}

	/**
	 * Test Hybrid\Acl::can() given 'mock-one'
	 *
	 * @test
	 */
	public function testCanSyncRole()
	{
		$acl1 = Hybrid\Acl::make('mock-one');
		$acl2 = Hybrid\Acl::make('mock-two');

		Hybrid\Acl::add_role('admin');
		Hybrid\Acl::add_role('manager');

		$this->assertTrue($acl1->has_role('admin'));
		$this->assertTrue($acl2->has_role('admin'));
		$this->assertTrue($acl1->has_role('manager'));
		$this->assertTrue($acl2->has_role('manager'));

		Hybrid\Acl::remove_role('manager');

		$this->assertTrue($acl1->has_role('admin'));
		$this->assertTrue($acl2->has_role('admin'));
		$this->assertFalse($acl1->has_role('manager'));
		$this->assertFalse($acl2->has_role('manager'));

		$this->assertTrue(is_array(Hybrid\Acl::all()));
		$this->assertFalse(array() === Hybrid\Acl::all());

		Hybrid\Acl::shutdown();

		$this->assertEquals(array(), Hybrid\Acl::all());
	}
	
}