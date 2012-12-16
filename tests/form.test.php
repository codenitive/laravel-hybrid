<?php

Bundle::start('hybrid');

class FormTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$mock_data = new Laravel\Fluent(array(
			'id' => 1, 
			'name' => 'Laravel'
		));

		Hybrid\Form::of('mock', function ($form) use ($mock_data)
		{
			$form->rows($mock_data);
			$form->attr(array(
				'method' => 'POST',
				'action' => 'http://localhost',
				'class'  => 'foo',
			));
		});

		Hybrid\Form::of('mock-2', function ($form) use ($mock_data)
		{
			$form->row($mock_data);
			$form->attr = array(
				'method' => 'POST',
				'action' => 'http://localhost',
				'class'  => 'foo'
			);
		});
	}

	/**
	 * test Hybrid\Table::make()
	 *
	 * @test
	 */
	public function testMake()
	{
		$this->assertInstanceOf('Hybrid\Form', Hybrid\Form::of('mock'));
		$this->assertInstanceOf('Hybrid\Form\Grid', Hybrid\Form::of('mock')->grid);
		$this->assertInstanceOf('Hybrid\Form\Grid', new Hybrid\Form\Grid);
	}

	/**
	 * test Hybrid\Table::render()
	 *
	 * @test
	 */
	public function testRender()
	{
		ob_start();
		echo Hybrid\Form::of('mock');
		$output = ob_get_contents();
		ob_end_clean();

		$expected = '<form class="form-horizontal" method="POST" action="http://localhost" accept-charset="UTF-8">
<div class="form-actions">
	<button type="submit" class="btn btn-primary">Submit</button>
</div>

</form>';

		$this->assertEquals($expected, $output);

		ob_start();
		echo Hybrid\Form::of('mock-2');
		$output = ob_get_contents();
		ob_end_clean();

		$expected = '<form class="form-horizontal" method="POST" action="http://localhost" accept-charset="UTF-8">
<div class="form-actions">
	<button type="submit" class="btn btn-primary">Submit</button>
</div>

</form>';

		$this->assertEquals($expected, $output);
	}


}