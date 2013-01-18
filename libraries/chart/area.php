<?php namespace Hybrid\Chart;

/**
 * AreaChart class using Google Visualization API
 *
 * @package    Hybrid\Chart
 * @category   Area
 * @author     Laravel Hybrid Development Team
 * @see        http://code.google.com/apis/visualization/documentation/gallery/areachart.html 
 */

use \Config;

class Area extends Driver {
	
	/**
	 * Chart name
	 *
	 * @var string
	 */
	protected $name = 'AreaChart';

	/**
	 * Initiate the instance during construct.
	 *
	 * @access public
	 * @return void
	 */
	public function initiate()
	{
		$this->put(Config::get('hybrid::chart.area', array()));
	}

}