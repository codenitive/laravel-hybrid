<?php namespace Hybrid\Chart;

/**
 * PieChart class using Google Visualization API
 *
 * @package    Hybrid\Chart
 * @category   Pie
 * @author     Laravel Hybrid Development Team
 * @see        http://code.google.com/apis/visualization/documentation/gallery/piechart.html 
 */

use \Config;

class Pie extends Driver {

	/**
	 * Chart name
	 *
	 * @var string
	 */
	protected $name = 'PieChart';

	/**
	 * Initiate the instance during construct.
	 *
	 * @access public
	 * @return void
	 */
	public function initiate()
	{
		$this->put(Config::get('hybrid::chart.pie', array()));
	}
}