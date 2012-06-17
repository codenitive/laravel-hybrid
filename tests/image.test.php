<?php

class TestImage extends PHPUnit_Framework_TestCase 
{
	/**
	 * Setup: Start Hybrid Bundle
	 *
	 * @return  void
	 */
	public function setup()
	{
		Bundle::start('hybrid');
	}
}