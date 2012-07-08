<?php

/*
|--------------------------------------------------------------------------
| Hybrid Library
|--------------------------------------------------------------------------
|
| Map Hybrid Library using PSR-0 standard namespace. 
 */
Autoloader::namespaces(array(
	'Hybrid' => Bundle::path('hybrid').'libraries',
));

/*
|--------------------------------------------------------------------------
| Hybrid Exceptions
|--------------------------------------------------------------------------
|
| List of exceptions for Hybrid bundle.
 */
Autoloader::map(array(
	'Hybrid\\AclException'             => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\Exception'                => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\InvalidArgumentException' => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\OutOfBoundsException'     => Bundle::path('hybrid').'libraries/exceptions'.EXT,
	'Hybrid\\RuntimeException'         => Bundle::path('hybrid').'libraries/exceptions'.EXT,
));

/*
|--------------------------------------------------------------------------
| Hybrid Events Listener
|--------------------------------------------------------------------------
|
| Lets listen to when Hybrid bundle is started and `hybrid.auth.roles` event.
 */ 
Event::listen('laravel.started: hybrid', function () { Hybrid\Core::start(); });

Event::listen('hybrid.auth.roles', function ($user, $roles)
{
	$callback = Config::get('hybrid::auth.roles');

	if (is_callable($callback))
	{
		return call_user_func($callback, $user, $roles);
	}
});