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

class Timeline extends Driver 
{
	public function __construct() 
	{
		parent::__construct();

		$this->put(Config::get('hybrid::chart.timeline', array()));
	}

	public function render($width = '100%', $height = '300px') 
	{
		$columns    = $this->columns;
		$rows       = $this->rows;

		$this->put('width', $width);
		$this->put('height', $height);

		$options    = json_encode($this->config);

		$id         = 'timeline_'.md5($columns.$rows.time().microtime());

		return <<<SCRIPT
<div id="{$id}" style="width:{$width}px; height:{$height}px;"></div>
<script type="text/javascript">
google.load("visualization", "1", {packages:["annotatedtimeline"]});
google.setOnLoadCallback(draw_{$id});
function draw_{$id}() {
	var data = new google.visualization.DataTable();
	{$columns}
	{$rows}
	
	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('{$id}'));
	chart.draw(data, {$options});
};
</script>
SCRIPT;
	}

}