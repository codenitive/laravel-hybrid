<?php namespace Hybrid;

/**
 * Chart class using Google Visualization API
 *
 * @package    Hybrid
 * @category   Chart
 * @author     Laravel Hybrid Development Team
 */

use Hybrid\Chart\Fluent;

class Chart {

	/**
	 * Cache Chart instance so we can reuse it on multiple request.
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $instances = array();

	/**
	 * Initiate a new Chart\Driver instance.
	 * 
	 * @static
	 * @access  public
	 * @param   string          $name
	 * @param   Chart\Fluent    $data
	 * @return  Chart\Driver 
	 * @throws  Exception
	 */
	public static function make($name, Fluent $data = null) 
	{
		if (is_null($name)) $name = 'default';

		$name = strtolower($name);

		if ( ! isset(static::$instances[$name]))
		{
			$class  = ucfirst($name);
			$driver = "Hybrid\Chart\\$class";
			
			if ( ! class_exists($driver))
			{
				throw new Exception("Requested Hybrid\Chart Driver [{$driver}] does not exist.");
			}

			static::$instances[$name] = new $driver($data);
		}

		return static::$instances[$name];
	}

	/**
	 * Hybrid\Chart doesn't support a construct method
	 *
	 * @access  public
	 */
	public function __construct() 
	{
		throw new RuntimeException("Hybrid\Chart doesn't support a construct method.");
	}
	
	/**
	 * Load Google JavaSript API Library
	 *
	 * @static
	 * @access  public
	 * @return  string
	 */
	public static function js() 
	{
		return '<script src="https://www.google.com/jsapi"></script>';
	}
}