<?php

Autoloader::map(array(
	'Hybrid\\Acl'          => Bundle::path('hybrid').'classes/acl'.EXT,
	'Hybrid\\AclException' => Bundle::path('hybrid').'classes/acl'.EXT,
	
	'Hybrid\\Auth' => Bundle::path('hybrid').'classes/auth'.EXT,

	'Hybrid\\Chart'        => Bundle::path('hybrid').'classes/chart'.EXT,
	'Hybrid\\Chart_Area'   => Bundle::path('hybrid').'classes/chart/area'.EXT,
	'Hybrid\\Chart_Driver' => Bundle::path('hybrid').'classes/chart/driver'.EXT,

	'Hybrid\\Memory'         => Bundle::path('hybrid').'classes/memory'.EXT,
	'Hybrid\\Memory_Driver'  => Bundle::path('hybrid').'classes/memory/driver'.EXT,
	'Hybrid\\Memory_Runtime' => Bundle::path('hybrid').'classes/memory/runtime'.EXT,

));