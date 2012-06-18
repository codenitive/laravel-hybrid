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

class Line extends Driver 
{
	public function __construct() 
	{
		parent::__construct();

		$this->put(Config::get('hybrid::chart.line', array()));
	}

	public function render($width = '100%', $height = '300px') 
	{
		$columns    = $this->columns;
		$rows       = $this->rows;

		$this->put('width', $width);
		$this->put('height', $height);

		$options    = json_encode($this->config);

		$id         = 'linechart_'.md5($columns.$rows.time().microtime());

		return <<<SCRIPT
<div id="{$id}"></div>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(draw_{$id});
function draw_{$id}() {
	var data = new google.visualization.DataTable();
	{$columns}
	{$rows}
	
	var chart = new google.visualization.LineChart(document.getElementById('{$id}'));
	chart.draw(data, {$options});
};
</script>
SCRIPT;
	}

}