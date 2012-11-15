<?php

class Hybrid_Memory_Task {

	/**
	 * Create memory table
	 * 
	 * @param   array   $arguments
	 * @return  void
	 */
	public function install($arguments)
	{
		$name = array_shift($arguments);

		if (empty($name)) $name = Config::get('hybrid::memory.default_table');

		Schema::create($name, function ($table)
		{
			$table->increments('id');

			$table->string('name', 64);
			$table->blob('value');

			$table->unique('name');
		});
	}

	/**
	 * Drop memory table
	 *
	 * @param   array   $arguments
	 * @return  void
	 */
	public function uninstall($arguments)
	{
		$name = array_shift($arguments);

		if (empty($name)) $name = Config::get('hybrid::memory.default_table');

		Schema::drop($name);
	}
}