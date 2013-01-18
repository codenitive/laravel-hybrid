<?php namespace Hybrid\Chart;

/**
 * Scatter Chart class using Google Visualization API
 *
 * @package    Hybrid\Chart
 * @category   Scatter
 * @author     Laravel Hybrid Development Team
 * @see        http://code.google.com/apis/visualization/documentation/gallery/scatterchart.html 
 */

use \Config;

class Scatter extends Driver {
		
	/**
	 * Chart name
	 *
	 * @var string
	 */
	protected $name = 'ScatterChart';

	/**
	 * Initiate the instance during construct.
	 *
	 * @access public
	 * @return void
	 */
	public function initiate()
	{
		$this->put(Config::get('hybrid::chart.scatter', array()));
	}

}