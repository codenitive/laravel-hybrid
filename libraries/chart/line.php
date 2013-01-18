<?php namespace Hybrid\Chart;

/**
 * LineChart class using Google Visualization API
 *
 * @package    Hybrid\Chart
 * @category   Line
 * @author     Laravel Hybrid Development Team
 * @see        http://code.google.com/apis/visualization/documentation/gallery/linechart.html 
 */

use \Config;

class Line extends Driver {
	
	/**
	 * Chart name
	 *
	 * @var string
	 */
	protected $name = 'LineChart';

	/**
	 * Initiate the instance during construct.
	 *
	 * @access public
	 * @return void
	 */
	public function initiate()
	{
		$this->put(Config::get('hybrid::chart.line', array()));
	}

}