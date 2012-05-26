<?php namespace Hybrid;

use \Event, \Exception;

class Memory
{
	protected static $initiated = false;

	/**
	 * Cache memory instance so we can reuse it
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $instances = array();

	/**
	 * Initiate a new Memory instance
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name      instance name
	 * @param   array   $config
	 * @return  Memory
	 * @throws  \Exception
	 */
	public static function make($name = null, $config = array())
	{
		if (false === static::$initiated)
		{
			Event::listen('laravel.done', function($response) { Memory::shutdown(); });

			static::$initiated = true;
		}

		if (is_null($name)) $name = 'runtime.default';

		if (false === strpos($name, '.')) $name = $name.'.default';

		list($storage, $_name) = explode('.', $name, 2);

		switch ($storage)
		{
			case 'fluent' :
				$storage = 'fluent';
				break;
			case 'eloquent' :
				$storage = 'eloquent';
				break;
			case 'runtime' :
			default :
				$storage = 'runtime';
			break;
		}

		$name = $storage.'.'.$_name;
		
		if ( ! isset(static::$instances[$name]))
		{
			$driver = "\Hybrid\Memory_".ucfirst($storage);

			// instance has yet to be initiated
			if ( ! class_exists($driver))
			{
				throw new Exception("Requested {$driver} does not exist.");
			}

			static::$instances[$name] = new $driver($_name, $config);
		}

		return static::$instances[$name];
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