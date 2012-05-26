<?php

Autoloader::namespaces(array(
	'Hybrid' => Bundle::path('hybrid').'libraries',
));

Autoloader::map(array(
	'Hybrid\\AclException'             => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\Exception'                => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\InvalidArgumentException' => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\OutOfBoundsException'     => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\RuntimeException'         => Bundle::path('hybrid').'libraries/exceptions'.EXT,
));

// Lets listen to when Hybrid bundle is started.
Event::listen('laravel.started: hybrid', function () { });