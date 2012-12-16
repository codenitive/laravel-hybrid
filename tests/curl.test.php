<?php

Bundle::start('hybrid');

class CurlTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test that Hybrid\Curl::make() return an instanceof Hybrid\Curl.
	 * 
	 * @test
	 * @return  void
	 */
	public function testMake()
	{
		$this->assertInstanceOf('Hybrid\Curl', Hybrid\Curl::make('GET http://google.com')); 
	}

	/**
	 * Test that Hybrid\Curl::make() return exception when given invalid driver
	 *
	 * @test
	 * @expectedException Hybrid\Exception
	 */
	public function testMakeExpectedException()
	{
		Hybrid\Curl::make('FORK http://google.com');
	}

}