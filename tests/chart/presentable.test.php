<?php

Bundle::start('hybrid');

class ChartPresentableTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Chart instance.
	 *
	 * @var Hybrid\Chart\Presentable
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = new Hybrid\Chart\Presentable;
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
			'Task'          => 'string',
			'Hours per Day' => 'number',
		));

		$this->assertEquals("data.addColumn('string', 'Task');\r\ndata.addColumn('number', 'Hours per Day');", 
			$this->stub->get_columns());
	}

	/**
	 * Test setting rows data
	 *
	 * @test
	 */
	public function testRows()
	{
		$this->stub->set_rows(array(
			'Work'  => array(11),
			'Sleep' => array(8),
			'Eat'   => array(0.5),
		));

		$this->assertEquals("data.addRows(3);\r\ndata.setValue(0, 0, 'Work');\r\ndata.setValue(0, 1, 11);\r\ndata.setValue(1, 0, 'Sleep');\r\ndata.setValue(1, 1, 8);\r\ndata.setValue(2, 0, 'Eat');\r\ndata.setValue(2, 1, 0.5);", 
			$this->stub->get_rows());
	}
}