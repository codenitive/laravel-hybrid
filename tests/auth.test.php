<?php

Bundle::start('hybrid');

class AuthTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Event::override('hybrid.auth.roles', function ($user_id, $roles)
		{
			return array('admin', 'editor');
		});
	}

	/**
	 * Test Hybrid\Auth::roles() returning valid roles
	 * 
	 * @test
	 */
	public function testRolesMethod()
	{
		$expected = array('admin', 'editor');
		$output   = Hybrid\Auth::roles();

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test Hybrid\Auth::is() returning valid roles
	 * 
	 * @test
	 */
	public function testIsMethod()
	{
		$this->assertTrue(Hybrid\Auth::is('admin'));
		$this->assertTrue(Hybrid\Auth::is('editor'));
		$this->assertFalse(Hybrid\Auth::is('user'));
	}
}