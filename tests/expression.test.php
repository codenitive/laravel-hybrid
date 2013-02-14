<?php namespace Hybrid\Tests;

\Bundle::start('hybrid');

class ExpressionTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test Laravel\Expression 
	 *
	 * @test
	 */
	public function testConstructReturnValid()
	{
		$expected = "foobar";
		$actual   = new \Hybrid\Expression($expected);

		$this->assertInstanceOf('\Hybrid\Expression', $actual);
		$this->assertEquals($expected, $actual);
		$this->assertEquals($expected, $actual->get());
	}
}