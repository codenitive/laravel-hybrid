<?php namespace Hybrid;

class Chart 
{
	/**
	 * Cache Chart instance so we can reuse it on multiple request.
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $instances = array();

	/**
	 * Initiate a new Chart_Driver instance.
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name
	 * @return  Chart_Driver 
	 * @throws  \Exception
	 */
	public static function make($name) 
	{
		if (is_null($name)) $name = 'default';

		$name = strtolower($name);

		if ( ! isset(static::$instances[$name]))
		{
			$driver = 'Chart\\'.ucfirst($name);
			
			if ( ! class_exists($driver))
			{
				throw new Exception("Requested Hybrid\Chart Driver [{$driver}] does not exist.");
			}

			static::$instances[$name] = new $driver();
		}

		return static::$instances[$name];
	}

	/**
	 * Hybrid\Chart doesn't support a construct method
	 *
	 * @access  protected
	 */
	protected function __construct() { }
	
	/**
	 * Load Google JavaSript API Library
	 *
	 * @static
	 * @access  public
	 * @return  string
	 */
	public static function js() 
	{
		return '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
	}
}