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

class Scatter extends Driver 
{
	public function __construct() 
	{
		parent::__construct();

		$this->put(Config::get('hybrid::chart.scatter', array()));
	}

	public function render($width = '100%', $height = '300px') 
	{
		$columns    = $this->columns;
		$rows       = $this->rows;

		$this->put('width', $width);
		$this->put('height', $height);

		$options    = json_encode($this->config);

		$id         = 'scatter_'.md5($columns.$rows.time().microtime());

		return <<<SCRIPT
<div id="{$id}" style="width:{$width}; height:{$height};"></div>
<script type="text/javascript">
google.load("visualization", "1", {packages:["table"]});
google.setOnLoadCallback(draw_{$id});
function draw_{$id}() {
	var data = new google.visualization.DataTable();
	{$columns}
	{$rows}
	
	var chart = new google.visualization.ScatterChart(document.getElementById('{$id}'));
	chart.draw(data, {$options});
};
</script>
SCRIPT;
	}

}