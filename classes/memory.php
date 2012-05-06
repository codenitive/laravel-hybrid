<?php

namespace Hybrid;

use \Event;
use \Exception;

class Memory
{
	/**
	 * Cache registry instance so we can reuse it
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $instances = array();

	protected static $initiated = false;

	/**
	 * Initiate a new Memory instance
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name       instance name
	 * @return  object
	 * @throws  \Exception
	 */
	public static function make($instance_name = null, $config = array())
	{
		if (false === static::$initiated)
		{
			Event::listen('laravel.done', function($response) 
			{
				Memory::shutdown();
			});

			static::$initiated = true;
		}

		if (is_null($instance_name))
		{
			$instance_name = 'runtime.default';
		}

		if (false === strpos($instance_name, '.'))
		{
			$instance_name = $instance_name.'.default';
		}

		list($storage, $name) = explode('.', $instance_name, 2);

		switch ($storage)
		{
			case 'runtime' :
			default :
				$storage = 'runtime';
			break;
		}

		$instance_name = $storage.'.'.$name;
		
		if ( ! isset(static::$instances[$instance_name]))
		{
			$driver = "\Hybrid\Memory_".ucfirst($storage);

			// instance has yet to be initiated
			if (class_exists($driver))
			{
				static::$instances[$instance_name] = new $driver($name, $config);
			}
			else
			{
				throw new Exception("Requested {$driver} does not exist.");
			}
		}

		return static::$instances[$instance_name];
	}

	/**
	 * Hybrid\Memory doesn't support a construct method
	 *
	 * @access  protected
	 */
	protected function __construct() {}

	/**
	 * Loop every instance and execute shutdown method (if available)
	 *
	 * @static
	 * @access  public
	 * @return  void
	 */
	public static function shutdown()
	{
		foreach (static::$instances as $name => $class)
		{
			$class->shutdown();
		}
	}
	
}