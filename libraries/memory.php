<?php namespace Hybrid;

/**
 * Memory class
 *
 * @package    Hybrid
 * @category   Memory
 * @author     Laravel Hybrid Development Team
 */

use \Closure, \Config, \Event;

class Memory {

	/**
	 * The third-party driver registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

	/**
	 * Memory initiated status
	 *
	 * @static
	 * @access  protected
	 * @var     boolean
	 */
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
	 * Run Memory start configuration once before doing anything else.
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function start()
	{
		if (true === static::$initiated) return true;

		Event::listen('laravel.done', function() 
		{ 
			Memory::shutdown(); 
		});

		return static::$initiated = true;
	}

	/**
	 * Register a third-party memory driver.
	 *
	 * @param  string   $driver
	 * @param  Closure  $resolver
	 * @return void
	 */
	public static function extend($driver, Closure $resolver)
	{
		static::$registrar[$driver] = $resolver;
	}

	/**
	 * Initiate a new Memory instance
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name      instance name
	 * @param   array   $config
	 * @return  Memory
	 * @throws  Exception
	 */
	public static function make($name = null, $config = array())
	{
		static::start();

		switch (true)
		{
			case (is_null($name)) :
				$name = 'runtime.default';
				break;
			case (false === strpos($name, '.')) : 
				$name = $name.'.default';
				break;
		}

		list($storage, $driver) = explode('.', $name, 2);

		$name = $storage.'.'.$driver;
		
		if ( ! isset(static::$instances[$name]))
		{
			if (isset(static::$registrar[$storage]))
			{
				$resolver = static::$registrar[$storage];

				return static::$instances[$name] = $resolver($driver, $config);
			}

			switch ($storage)
			{
				case 'fluent' :
					if ($driver === 'default') $driver = Config::get('hybrid::memory.default_table');
					static::$instances[$name] = new Memory\Fluent($driver, $config);
					break;
				case 'eloquent' :
					if ($driver === 'default') $driver = Config::get('hybrid::memory.default_model');
					static::$instances[$name] = new Memory\Eloquent($driver, $config);
					break;
				case 'cache' :
					static::$instances[$name] = new Memory\Cache($driver, $config);
					break;
				case 'runtime' :
					static::$instances[$name] = new Memory\Runtime($driver, $config);
					break;
				default :
					throw new Exception("Requested Hybrid\Memory Driver [$storage] does not exist.");
			}
		}

		return static::$instances[$name];
	}

	/**
	 * Hybrid\Memory doesn't support a construct method
	 *
	 * @access  public
	 * @throws  Hybrid\RuntimeException
	 */
	public function __construct() 
	{
		throw new RuntimeException("Hybrid\Memory doesn't support a construct method.");
	}

	/**
	 * Loop every instance and execute shutdown method (if available)
	 *
	 * @static
	 * @access  public
	 * @return  void
	 */
	public static function shutdown()
	{
		foreach (static::$instances as $class) $class->shutdown();

		static::$instances = array();
	}
	
}