<?php

class TestCurl extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup: Start Hybrid Bundle
	 *
	 * @return  void
	 */
	public function setup()
	{
		Bundle::start('hybrid');
	}

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