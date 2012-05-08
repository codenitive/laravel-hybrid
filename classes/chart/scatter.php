<?php namespace Hybrid;

use \Config;

class Chart_Scatter extends Chart_Driver 
{
	public function __construct() 
	{
		parent::__construct();

		$this->configure(Config::get('hybrid::chart.scatter', array()));
	}

	public function render($width = '100%', $height = '300px') 
	{
		$columns    = $this->columns;
		$rows       = $this->rows;

		$this->configure('width', $width);
		$this->configure('height', $height);

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