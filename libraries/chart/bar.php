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
	
	public function __construct() 
	{
		parent::__construct();

		$this->put(Config::get('hybrid::chart.bar', array()));
	}

	public function render($width = '100%', $height = '300px') 
	{
		$columns    = $this->columns;
		$rows       = $this->rows;

		$this->put('width', $width);
		$this->put('height', $height);

		$options    = json_encode($this->config);

		$id         = 'barchart_'.md5($columns.$rows.time().microtime());

		return <<<SCRIPT
<div id="{$id}"></div>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(draw_{$id});
function draw_{$id}() {
	var data = new google.visualization.DataTable();
	{$columns}
	{$rows}
	
	var chart = new google.visualization.BarChart(document.getElementById('{$id}'));
	chart.draw(data, {$options});
};
</script>
SCRIPT;
	}

}