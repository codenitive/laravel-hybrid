# Hybrid Memory

## Table of Contents
 
- [Introduction](#introduction)
- [Methods](#methods) 

<a name="introduction"></a>
## Introduction

`Hybrid\Memory` handle runtime configuration either using 'in memory' Runtime or database using Fluent Query Builder or Eloquent ORM.

	$runtime  = Hybrid\Memory::make('runtime');
	$fluent   = Hybrid\Memory::make('fluent');
	$eloquent = Hybrid\Memory::make('eloquent'); 
	$cache    = Hybrid\Memory::make('cache');

### Default Configuration for Database

You can customize the table name or class name using `hybrid/config/memory.php`.

	/*
	|--------------------------------------------------------------------------
	| Default Memory Model
	|--------------------------------------------------------------------------
	|
	| When using the "eloquent" memory driver, you may specify the
	| model that should be considered the "Option" model. This model will
	| be used to store and load the memory/config of your application.
	|
	*/

	'default_model' => 'Option',

	/*
	|--------------------------------------------------------------------------
	| Default Memory Table
	|--------------------------------------------------------------------------
	|
	| When using the "fluent" memory driver, the database table used
	| to load memory may be specified here. This table will be used in by
	| the fluent query builder to store and load your memory/config.
	|
	*/

	'default_table' => 'options',
	
### Using different table or class

Other than the default, you can also use following method.

	// use table `module_options`
	$module = Hybrid\Memory::make('fluent.module_options');
	
	// use class `Configuration`
	$configuration = Hybrid\Memory::make('eloquent.Configuration');
	
### Example

	$memory = Hybrid\Memory::make('fluent');
	
	$memory->put('foo.bar', 'hello world');
	
	$callback = function ()
	{
		// different scope
		echo Hybrid\Memory::make('fluent')->get('foo.bar'); // return 'hello world'
	};
	
<a name="methods"></a>
## Methods

### make($name, $config)

Initiate a new `Hybrid\Memory` instance

	@static
	@param   string  $name      instance name
	@param   array   $config
	@return  Memory\Driver
	@throws  Hybrid\Exception
	
	$memory = Hybrid\Memory::make('fluent');

### put($key, $value)

Set a value from a key

	@param   string  $key        A string of key to add the value.
	@param   mixed   $value      The value.
	@return  mixed
	
	$memory->put('foo.bar', 'hello world');

### get($key, $default)

Get value of a key
	
	@param   string  $key        A string of key to search.
	@param   mixed   $default    Default value if key doesn't exist.
	@return  mixed
	
	$memory->get('foo'); // return array('bar' => 'hello world');
	
	$memory->get('foo.bar'); // return 'hello world'

### forget($key)

Delete value of a key
	
	@param   string  $key        A string of key to delete.
	@return  bool
	
	$memory->forget('foo');