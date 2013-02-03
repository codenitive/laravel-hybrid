<?php

Bundle::start('hybrid');

class ExpressionTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Test Laravel\Expression 
	 *
	 * @group laravel
	 */
	public function testConstructReturnValid()
	{
		$expected = "foobar";
		$actual   = new Hybrid\Expression($expected);

		$this->assertInstanceOf('Hybrid\Expression', $actual);
		$this->assertEquals($expected, $actual);
		$this->assertEquals($expected, $actual->get());
	}
}