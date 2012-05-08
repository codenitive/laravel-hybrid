<?php namespace Hybrid;

use \Config;

class Chart_Area extends Chart_Driver 
{
	public function __construct() 
	{
		parent::__construct();

		$this->configure(Config::get('hybrid::chart.area', array()));
	}

	public function render($width = '100%', $height = '300px') 
	{
		$columns    = $this->columns;
		$rows       = $this->rows;

		$this->configure('width', $width);
		$this->configure('height', $height);

		$options    = json_encode($this->options);

		$id         = 'areachart_'.md5($columns.$rows.time().microtime());

		return <<<SCRIPT
<div id="{$id}"></div>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(draw_{$id});
function draw_{$id}() {
	var data = new google.visualization.DataTable();
	{$columns}
	{$rows}
	
	var chart = new google.visualization.AreaChart(document.getElementById('{$id}'));
	chart.draw(data, {$options});
};
</script>
SCRIPT;
	}

}