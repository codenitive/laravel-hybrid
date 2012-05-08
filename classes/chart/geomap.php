<?php namespace Hybrid;

use \Config;

class Chart_GeoMap extends Chart_Driver 
{
	public function __construct() 
	{
		parent::__construct();

		$this->configure(Config::get('hybrid::chart.geomap', array()));
	}

	public function render($width = '100%', $height = '300px') 
	{
		$columns    = $this->columns;
		$rows       = $this->rows;

		$this->configure('width', $width);
		$this->configure('height', $height);

		$options    = json_encode($this->config);

		$id         = 'geomap_'.md5($columns.$rows.time().microtime());

		return <<<SCRIPT
<div id="{$id}" style="width:{$width}px; height:{$height}px;"></div>
<script type="text/javascript">
google.load('visualization', '1', {'packages': ['geomap']});

google.setOnLoadCallback(draw_{$id});
function draw_{$id}() {
	var data = new google.visualization.DataTable();
	{$columns}
	{$rows}
	
	var geomap = new google.visualization.GeoMap(document.getElementById('{$id}'));
	geomap.draw(data, {$options});
};
</script>
SCRIPT;
	}

}