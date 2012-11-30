<?php

class FtpTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('hybrid');
	}

	/**
	 * Test instance of Hybrid\FTP
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$this->assertInstanceOf('Hybrid\FTP', Hybrid\FTP::make());
	}
}