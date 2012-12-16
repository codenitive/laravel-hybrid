<?php

Bundle::start('hybrid');

class FtpTest extends PHPUnit_Framework_TestCase {

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