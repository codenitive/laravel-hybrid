<?php namespace Hybrid\Tests;

\Bundle::start('hybrid');

class CurlTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test that Hybrid\Curl::make() return an instanceof Hybrid\Curl.
	 * 
	 * @test
	 */
	public function testMake()
	{
		$this->assertInstanceOf('\Hybrid\Curl', 
			\Hybrid\Curl::make('GET http://google.com')); 
	}

	/**
	 * Test that Hybrid\Curl::make() return exception when given invalid driver
	 *
	 * @test
	 * @expectedException \Hybrid\Exception
	 */
	public function testMakeThrowsExpectedException()
	{
		\Hybrid\Curl::make('FORK http://google.com');
	}

}