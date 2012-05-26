<?php

Autoloader::directories(array(
	Bundle::path('hybrid').'libraries',
));

Autoloader::map(array(
	'Hybrid\\AclException'             => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\Exception'                => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\InvalidArgumentException' => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\OutOfBoundsException'     => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\RuntimeException'         => Bundle::path('hybrid').'libraries/exceptions'.EXT,
));

Autoloader::map(array(
	'Hybrid\\Chart'          => Bundle::path('hybrid').'libraries/chart'.EXT,
	'Hybrid\\Chart_Area'     => Bundle::path('hybrid').'libraries/chart/area'.EXT,
	'Hybrid\\Chart_Bar'      => Bundle::path('hybrid').'libraries/chart/bar'.EXT,
	'Hybrid\\Chart_Driver'   => Bundle::path('hybrid').'libraries/chart/driver'.EXT,
	'Hybrid\\Chart_GeoMap'   => Bundle::path('hybrid').'libraries/chart/geomap'.EXT,
	'Hybrid\\Chart_Line'     => Bundle::path('hybrid').'libraries/chart/line'.EXT,
	'Hybrid\\Chart_Pie'      => Bundle::path('hybrid').'libraries/chart/pie'.EXT,
	'Hybrid\\Chart_Scatter'  => Bundle::path('hybrid').'libraries/chart/scatter'.EXT,
	'Hybrid\\Chart_Table'    => Bundle::path('hybrid').'libraries/chart/table'.EXT,
	'Hybrid\\Chart_Timeline' => Bundle::path('hybrid').'libraries/chart/timeline'.EXT,
	
	'Hybrid\\Image'             =>  Bundle::path('hybrid').'libraries/image'.EXT,
	'Hybrid\\Image_Driver'      =>  Bundle::path('hybrid').'libraries/driver'.EXT,
	'Hybrid\\Image_Gd'          =>  Bundle::path('hybrid').'libraries/gd'.EXT,
	'Hybrid\\Image_Imagemagick' =>  Bundle::path('hybrid').'libraries/imagemagick'.EXT,
	'Hybrid\\Image_Imagick'     =>  Bundle::path('hybrid').'libraries/imagick'.EXT,

));

// Lets listen to when Hybrid bundle is started.
Event::listen('laravel.started: hybrid', function () { Hybrid\Core::start(); });