<?php

Bundle::start('hybrid');

class ChartTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Test Hybrid\Chart::make() return an instanceof Hybrid\Chart.
	 *
	 * @test
	 */
	public function testMake()
	{
		$this->assertInstanceOf('Hybrid\Chart\Area', Hybrid\Chart::make('area'));
		$this->assertInstanceOf('Hybrid\Chart\Bar', Hybrid\Chart::make('bar'));
		$this->assertInstanceOf('Hybrid\Chart\GeoMap', Hybrid\Chart::make('geoMap'));
		$this->assertInstanceOf('Hybrid\Chart\Line', Hybrid\Chart::make('line'));
		$this->assertInstanceOf('Hybrid\Chart\Pie', Hybrid\Chart::make('pie'));
		$this->assertInstanceOf('Hybrid\Chart\Table', Hybrid\Chart::make('table'));
		$this->assertInstanceOf('Hybrid\Chart\Timeline', Hybrid\Chart::make('timeline'));
	}

	/**
	 * Test Chart::make() given invalid driver
	 *
	 * @expectedException Hybrid\Exception
	 */
	public function testMakeThrowsExpectedException()
	{
		Hybrid\Chart::make('date');
	}

	/**
	 * Test Chart::make() given invalid driver
	 *
	 * @expectedException Hybrid\RuntimeException
	 */
	public function testConstructThrowsExpectedException()
	{
		$stub =new Hybrid\Chart;
	}

	/**
	 * Test Hybrid\Chart::js()
	 *
	 * @test
	 */
	public function testLoadJavaScript()
	{
		$expected = '<script src="https://www.google.com/jsapi"></script>';
		$output   = Hybrid\Chart::js();

		$this->assertEquals($expected, $output);
	}

 }