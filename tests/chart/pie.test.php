<?php namespace Hybrid\Tests\Chart;

\Bundle::start('hybrid');

class PieTest extends \PHPUnit_Framework_TestCase {

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
		$this->chart = new \Hybrid\Chart\Pie;
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
		$this->assertInstanceOf('\Hybrid\Chart\Pie', $this->chart);
	}

	/**
	 * Test generated UUID is always unique
	 *
	 * @test
	 */
	public function testGeneratedUUID()
	{
		$this->assertContains('PieChart_', $this->chart->uuid());
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
		$this->assertContains("new google.visualization.PieChart(document.getElementById('{$uuid}')", $output);
	}
}