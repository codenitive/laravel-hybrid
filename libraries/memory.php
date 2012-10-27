<?php namespace Hybrid;

/**
 * Memory class
 *
 * @package    Hybrid
 * @category   Memory
 * @author     Laravel Hybrid Development Team
 */

use \Config, \Event;

class Memory
{
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
	 * @access protected
	 * @return void
	 */
	protected static function start()
	{
		if (false === static::$initiated)
		{
			Event::listen('laravel.done', function($response) { Memory::shutdown(); });

			static::$initiated = true;
		}
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

		if (is_null($name)) $name = 'runtime.default';

		if (false === strpos($name, '.')) $name = $name.'.default';

		list($storage, $driver) = explode('.', $name, 2);

		$name = $storage.'.'.$driver;
		
		if ( ! isset(static::$instances[$name]))
		{
			if (isset(static::$registrar[$driver]))
			{
				$resolver = static::$registrar[$driver];

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