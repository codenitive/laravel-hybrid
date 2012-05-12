<?php

Autoloader::map(array(
	'Hybrid\\Acl'          => Bundle::path('hybrid').'classes/acl'.EXT,
	'Hybrid\\AclException' => Bundle::path('hybrid').'classes/acl'.EXT,
	
	'Hybrid\\Auth' => Bundle::path('hybrid').'classes/auth'.EXT,

	'Hybrid\\Chart'          => Bundle::path('hybrid').'classes/chart'.EXT,
	'Hybrid\\Chart_Area'     => Bundle::path('hybrid').'classes/chart/area'.EXT,
	'Hybrid\\Chart_Bar'      => Bundle::path('hybrid').'classes/chart/bar'.EXT,
	'Hybrid\\Chart_Driver'   => Bundle::path('hybrid').'classes/chart/driver'.EXT,
	'Hybrid\\Chart_GeoMap'   => Bundle::path('hybrid').'classes/chart/geomap'.EXT,
	'Hybrid\\Chart_Line'     => Bundle::path('hybrid').'classes/chart/line'.EXT,
	'Hybrid\\Chart_Pie'      => Bundle::path('hybrid').'classes/chart/pie'.EXT,
	'Hybrid\\Chart_Scatter'  => Bundle::path('hybrid').'classes/chart/scatter'.EXT,
	'Hybrid\\Chart_Table'    => Bundle::path('hybrid').'classes/chart/table'.EXT,
	'Hybrid\\Chart_Timeline' => Bundle::path('hybrid').'classes/chart/timeline'.EXT,

	'Hybrid\\Memory'          => Bundle::path('hybrid').'classes/memory'.EXT,
	'Hybrid\\Memory_Driver'   => Bundle::path('hybrid').'classes/memory/driver'.EXT,
	'Hybrid\\Memory_Eloquent' => Bundle::path('hybrid').'classes/memory/eloquent'.EXT,
	'Hybrid\\Memory_Fluent'   => Bundle::path('hybrid').'classes/memory/fluent'.EXT,
	'Hybrid\\Memory_Runtime'  => Bundle::path('hybrid').'classes/memory/runtime'.EXT,

));