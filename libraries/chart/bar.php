<?php namespace Hybrid\Chart;

/**
 * BarChart class using Google Visualization API
 *
 * @package    Hybrid\Chart
 * @category   Bar
 * @author     Laravel Hybrid Development Team
 * @see        http://code.google.com/apis/visualization/documentation/gallery/barchart.html 
 */

use \Config;

class Bar extends Driver {
	
	/**
	 * Chart name
	 *
	 * @var string
	 */
	protected $name = 'BarChart';

	/**
	 * Initiate the instance during construct.
	 *
	 * @access public
	 * @return void
	 */
	public function initiate()
	{
		$this->put(Config::get('hybrid::chart.bar', array()));
	}
}