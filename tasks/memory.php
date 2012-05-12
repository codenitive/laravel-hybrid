<?php

class Hybrid_Memory_Task
{
	/**
	 * Create memory table
	 * 
	 * @param  array    $arguments
	 * @return void
	 */
	public function install($arguments)
	{
		$name = array_shift($arguments);

		if (empty($name)) $name = 'options';

		Schema::create($name, function ($table)
		{
			$table->increments('id');

			$table->string('name', '255');
			$table->blob('value');

			$table->unique('name');
		});
	}
}