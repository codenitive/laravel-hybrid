<?php namespace Hybrid\Tests\Chart;

\Bundle::start('hybrid');

class ScatterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Chart instance
	 * 
	 * @var DriverStub
	 */
	private $chart = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->chart = new \Hybrid\Chart\Scatter;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		$this->chart = null;
	}

	/**
	 * Test object is an instance of Hybrid\Chart\Driver.
	 *
	 * @test
	 */
	public function testObjectInstanceOf()
	{
		$this->assertInstanceOf('\Hybrid\Chart\Driver', $this->chart);
		$this->assertInstanceOf('\Hybrid\Chart\Scatter', $this->chart);
	}

	/**
	 * Test generated UUID is always unique
	 *
	 * @test
	 */
	public function testGeneratedUUID()
	{
		$this->assertContains('ScatterChart_', $this->chart->uuid());
	}

	/**
	 * Test render is output the correct value
	 *
	 * @test
	 */
	public function testRenderIsOutputCorrectly()
	{
		$uuid   = $this->chart->uuid();
		$output = $this->chart->render();

		$this->assertContains('<div id="'.$uuid.'">', $output);
		$this->assertContains("new google.visualization.ScatterChart(document.getElementById('{$uuid}')", $output);
	}
}