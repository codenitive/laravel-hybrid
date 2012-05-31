<?php

class TestAcl extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup the test
	 */
	public function setup()
	{
		Bundle::start('hybrid');

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
		$this->assertInstanceOf('Hybrid\Acl', Hybrid\Acl::make('mock-one'));
	}

	/**
	 * Test Hybrid\Acl::can() given 'mock-one'
	 *
	 * @test
	 */
	public function testCanMockOne()
	{
		$acl    = Hybrid\Acl::make('mock-one');
		
		$output = $acl->can('view blog');
		$this->assertTrue($output);
		
		$output = $acl->can('view forum');
		$this->assertFalse($output);

		$output = $acl->can('view news');
		$this->assertFalse($output);
	}
	
}