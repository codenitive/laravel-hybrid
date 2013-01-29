<?php

class ChartDriverTest extends PHPUnit_Framework_TestCase {
	
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
		$this->chart = new ChartStub(new Hybrid\Chart\Fluent);

		$this->chart->put(array(
			'foo' => 'foobar',
		));

		$this->chart->put('foobar', 'foo');
		$this->chart->hello = 'hello world';
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		$this->chart = null;
	}

	/**
	 * Test object is an instance of Laravie\Chartie\Driver.
	 *
	 * @test core
	 */
	public function testObjectIsInstanceOfChartieDriver()
	{
		$this->assertInstanceOf('Hybrid\Chart\Driver', $this->chart);
	}

	/**
	 * Test generated UUID
	 *
	 * @test core
	 */
	public function testGeneratedUUID()
	{
		$this->assertContains('stub_', $this->chart->uuid());
	}

	/**
	 * Test generated UUID is always unique
	 *
	 * @test core
	 */
	public function testGeneratedUUIDIsAlwaysUnique()
	{
		$second = new ChartStub;

		$this->assertTrue($second->uuid() !== $this->chart->uuid());
	}

	/**
	 * Test setting attributes properly
	 * 
	 * @test core
	 */
	public function testPutAttributes()
	{
		$this->assertEquals('foobar', $this->chart->attributes['foo']);
		$this->assertEquals('foo', $this->chart->attributes['foobar']);
		$this->assertEquals('hello world', $this->chart->attributes['hello']);
	}

	/**
	 * Test get attributes properly
	 * 
	 * @test core
	 */
	public function testGetAttributes()
	{
		$this->assertEquals('foobar', $this->chart->foo);
		$this->assertEquals('foo', $this->chart->foobar);
		$this->assertEquals('hello world', $this->chart->hello);
	}

	/**
	 * Test setting attributes causing an exception
	 *
	 * @test core
	 * @expectedException InvalidArgumentException
	 */
	public function testPutAttributesFailed()
	{
		$this->chart->put(10, 'foobar');
	}

	/**
	 * Test __toString render properly
	 *
	 * @test core
	 */
	public function testToString()
	{
		$this->assertEquals("render stub", $this->chart."");
	}

	/**
	 * Test render is output the correct value
	 *
	 * @test core
	 */
	public function testRenderIsOutputCorrectly()
	{
		$this->assertEquals("render stub", $this->chart->render());
	}
}

class ChartStub extends Hybrid\Chart\Driver {

	protected $name = 'stub';

	public function initiate() {}

	public function render() 
	{
		return "render stub";
	}

}