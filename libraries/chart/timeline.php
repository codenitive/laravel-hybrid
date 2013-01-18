<?php namespace Hybrid\Chart;

/**
 * Timeline class using Google Visualization API
 *
 * @package    Hybrid\Chart
 * @category   Timeline
 * @author     Laravel Hybrid Development Team
 * @see        http://code.google.com/apis/visualization/documentation/gallery/annotatedtimeline.html 
 */

use \Config;

class Timeline extends Driver {

	/**
	 * Chart name
	 *
	 * @var string
	 */
	protected $name = 'AnnotatedTimeLine';

	/**
	 * Initiate the instance during construct.
	 *
	 * @access public
	 * @return void
	 */
	public function initiate()
	{
		$this->put(Config::get('hybrid::chart.timeline', array()));
	}

}