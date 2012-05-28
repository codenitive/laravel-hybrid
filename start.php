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
Event::listen('laravel.started: hybrid', function () { Hybrid\Core::start(); });

Event::listen('hybrid.auth.roles', function ($user_id, $roles)
{
	$callback = Config::get('hybrid::auth.roles');

	if ($callback instanceof \Closure)
	{
		return $callback($user_id, $roles);
	}
});