<?php namespace Hybrid\Chart;

/**
 * GeoMap Chart class using Google Visualization API
 *
 * @package    Hybrid\Chart
 * @category   GeoMap
 * @author     Laravel Hybrid Development Team
 * @see        http://code.google.com/apis/visualization/documentation/gallery/geomap.html 
 */

use \Config;

class GeoMap extends Driver {
	
	/**
	 * Chart name
	 *
	 * @var string
	 */
	protected $name = 'GeoMap';

	/**
	 * Initiate the instance during construct.
	 *
	 * @access public
	 * @return void
	 */
	public function initiate()
	{
		$this->put(Config::get('hybrid::chart.geomap', array()));
	}

}