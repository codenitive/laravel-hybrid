<?php

/*
|--------------------------------------------------------------------------
| Hybrid Library
|--------------------------------------------------------------------------
|
| Map Hybrid Library using PSR-0 standard namespace. 
|
*/

Autoloader::namespaces(array(
	'Hybrid' => Bundle::path('hybrid').'libraries'.DS,
));

/*
|--------------------------------------------------------------------------
| Hybrid Exceptions
|--------------------------------------------------------------------------
|
| List of exceptions for Hybrid bundle.
|
*/

Autoloader::map(array(
	'Hybrid\AclException'             => Bundle::path('hybrid').'libraries'.DS.'exceptions'.EXT,
	'Hybrid\Exception'                => Bundle::path('hybrid').'libraries'.DS.'exceptions'.EXT,
	'Hybrid\InvalidArgumentException' => Bundle::path('hybrid').'libraries'.DS.'exceptions'.EXT,
	'Hybrid\OutOfBoundsException'     => Bundle::path('hybrid').'libraries'.DS.'exceptions'.EXT,
	'Hybrid\RuntimeException'         => Bundle::path('hybrid').'libraries'.DS.'exceptions'.EXT,
	'Hybrid\FTP\RuntimeException'     => Bundle::path('hybrid').'libraries'.DS.'ftp'.DS.'exceptions'.EXT,
	'Hybrid\FTP\ServerException'      => Bundle::path('hybrid').'libraries'.DS.'ftp'.DS.'exceptions'.EXT,
));

/*
|--------------------------------------------------------------------------
| Hybrid Events Listener
|--------------------------------------------------------------------------
|
| Lets listen for `hybrid.auth.roles` event.
|
*/ 

Event::listen('hybrid.auth.roles', function ($user, $roles)
{
	$callback = Config::get('hybrid::auth.roles');

	if (is_callable($callback))
	{
		return call_user_func($callback, $user, $roles);
	}
});

/*
|--------------------------------------------------------------------------
| Hybrid IoC
|--------------------------------------------------------------------------
|
| Add IoC for request to View, allow other bundle to implement alternative 
| template option.
|
*/

IoC::register('hybrid.view', function($view)
{
	return View::make($view);
});
