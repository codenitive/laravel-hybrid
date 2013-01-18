<?php namespace Hybrid\Chart;

/**
 * Table class using Google Visualization API
 *
 * @package    Hybrid\Chart
 * @category   Table
 * @author     Laravel Hybrid Development Team
 * @see        http://code.google.com/apis/visualization/documentation/gallery/table.html 
 */

use \Config;

class Table extends Driver {

	/**
	 * Chart name
	 *
	 * @var string
	 */
	protected $name = 'Table';

	/**
	 * Initiate the instance during construct.
	 *
	 * @access public
	 * @return void
	 */
	public function initiate()
	{
		$this->put(Config::get('hybrid::chart.table', array()));
	}

}