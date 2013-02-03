<?php

Bundle::start('hybrid');

class ChartFluentTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Chart instance.
	 *
	 * @var Hybrid\Chart\Fluent
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = new Hybrid\Chart\Fluent;
	}

	/**
	* Teardown the test environment.
	*/
	public function tearDown()
	{
		$this->stub = null;
	}

	/**
	 * Test setting columns data
	 *
	 * @test
	 */
	public function testColumns()
	{
		$this->stub->set_columns(array(
			'Date'          => 'date',
			'Task'          => 'string',
			'Hours per Day' => 'number',
		));

		$refl = new \ReflectionObject($this->stub);
		$columns = $refl->getProperty('columns');
		$columns->setAccessible(true);

		$expected = array(
			"data.addColumn('date', 'Date');",
			"data.addColumn('string', 'Task');",
			"data.addColumn('number', 'Hours per Day');",
		);

		$this->assertEquals(implode("\r\n", $expected), $this->stub->get_columns());
		$this->assertEquals($expected, $columns->getValue($this->stub));

		$this->assertTrue(is_string($this->stub->get_columns()));
	}

	/**
	 * Test setting rows data
	 *
	 * @test
	 */
	public function testRows()
	{
		$this->stub->set_rows(array(
			'Work'  => array('2011-01-01 00:00:00', 11),
			'Sleep' => array('2011-01-01 00:00:00', 8),
			'Eat'   => array('2011-01-01 00:00:00', 0.5),
		));

		$expected = array(
			"data.addRows(3);",
			"data.setValue(0, 0, 'Work');",
			"data.setValue(0, 1, 2011-01-01 00:00:00);",
			"data.setValue(0, 2, 11);",
			"data.setValue(1, 0, 'Sleep');",
			"data.setValue(1, 1, 2011-01-01 00:00:00);",
			"data.setValue(1, 2, 8);",
			"data.setValue(2, 0, 'Eat');",
			"data.setValue(2, 1, 2011-01-01 00:00:00);",
			"data.setValue(2, 2, 0.5);"
		);

		$refl = new \ReflectionObject($this->stub);
		$rows = $refl->getProperty('rows');
		$rows->setAccessible(true);

		$this->assertEquals(implode("\r\n", $expected), $this->stub->get_rows());
		$this->assertEquals($expected, $rows->getValue($this->stub));
		$this->assertTrue(is_string($this->stub->get_rows()));
	}

	/**
	 * Test exporting to a chart.
	 *
	 * @test
	 */
	public function testExport()
	{
		$this->assertInstanceOf('Hybrid\Chart\Area', $this->stub->export('area'));
		$this->assertInstanceOf('Hybrid\Chart\Bar', $this->stub->export('bar'));
		$this->assertInstanceOf('Hybrid\Chart\GeoMap', $this->stub->export('geoMap'));
		$this->assertInstanceOf('Hybrid\Chart\Pie', $this->stub->export('pie'));
		$this->assertInstanceOf('Hybrid\Chart\Scatter', $this->stub->export('scatter'));
		$this->assertInstanceOf('Hybrid\Chart\Table', $this->stub->export('table'));
		$this->assertInstanceOf('Hybrid\Chart\Timeline', $this->stub->export('timeline'));
	}
}