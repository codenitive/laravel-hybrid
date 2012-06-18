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
		if (false === static::$initiated)
		{
			Event::listen('laravel.done', function($response) { Memory::shutdown(); });

			static::$initiated = true;
		}

		if (is_null($name)) $name = 'runtime.default';

		if (false === strpos($name, '.')) $name = $name.'.default';

		list($storage, $_name) = explode('.', $name, 2);

		$name = $storage.'.'.$_name;
		
		if ( ! isset(static::$instances[$name]))
		{
			switch ($storage)
			{
				case 'fluent' :
					if ($_name === 'default') $_name = Config::get('hybrid::memory.default_table');
					static::$instances[$name] = new Memory\Fluent($_name, $config);
					break;
				case 'eloquent' :
					if ($_name === 'default') $_name = Config::get('hybrid::memory.default_model');
					static::$instances[$name] = new Memory\Eloquent($_name, $config);
					break;
				case 'runtime' :
					static::$instances[$name] = new Memory\Runtime($_name, $config);
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