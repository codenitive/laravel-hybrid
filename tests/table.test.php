<?php

Bundle::start('hybrid');

class TableTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$mock_data = array(
			new Laravel\Fluent(array('id' => 1, 'name' => 'Laravel')),
			new Laravel\Fluent(array('id' => 2, 'name' => 'Illuminate')),
			new Laravel\Fluent(array('id' => 3, 'name' => 'Symfony')),
		);

		Hybrid\Table::of('mock', function ($table) use ($mock_data)
		{
			$table->rows($mock_data);
			$table->attr(array('class' => 'foo'));

			$table->column('id');
			$table->column('name');
		});

		Hybrid\Table::of('mock-2', function ($table) use ($mock_data)
		{
			$table->rows($mock_data);
			$table->attr = array('class' => 'foo');

			$table->column('id');
			$table->column('name', function ($column)
			{
				$column->value = function ($row)
				{
					return '<strong>'.$row->name.'</strong>';
				};
			});
		});
	}

	/**
	 * test Hybrid\Table::make()
	 *
	 * @test
	 */
	public function testMake()
	{
		$table = Hybrid\Table::make(function ($table) 
		{
			$table->attr = array('class' => 'foo');
		});

		$this->assertInstanceOf('Hybrid\Table', $table);
		$this->assertInstanceOf('Hybrid\Table', Hybrid\Table::of('mock'));
		$this->assertInstanceOf('Hybrid\Table\Grid', $table->grid);
		$this->assertInstanceOf('Hybrid\Table\Grid', new Hybrid\Table\Grid);
	}

	/**
	 * test Hybrid\Table::render()
	 *
	 * @test
	 */
	public function testRender()
	{
		ob_start();
		echo Hybrid\Table::of('mock');
		$output = ob_get_contents();
		ob_end_clean();

		$expected = '<table class="foo">
	<thead>
		<tr>
			<th>Id</th>
			<th>Name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td>Laravel</td>
		</tr>
		<tr>
			<td>2</td>
			<td>Illuminate</td>
		</tr>
		<tr>
			<td>3</td>
			<td>Symfony</td>
		</tr>
	</tbody>
</table>
';

		$this->assertEquals($expected, $output);

		ob_start();
		echo Hybrid\Table::of('mock-2');
		$output = ob_get_contents();
		ob_end_clean();

		$expected = '<table class="foo">
	<thead>
		<tr>
			<th>Id</th>
			<th>Name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td><strong>Laravel</strong></td>
		</tr>
		<tr>
			<td>2</td>
			<td><strong>Illuminate</strong></td>
		</tr>
		<tr>
			<td>3</td>
			<td><strong>Symfony</strong></td>
		</tr>
	</tbody>
</table>
';

		$this->assertEquals($expected, $output);
	}


}