<?php namespace Hybrid\Chart;

/**
 * Driver for Chart class using Google Visualization API
 *
 * @abstract
 * @package    Hybrid\Chart
 * @category   Driver
 * @author     Laravel Hybrid Development Team
 */

use \Config, \InvalidArgumentException;

abstract class Driver {
	
	/**
	* Chart name
	*
	* @var string
	*/
	protected $name = null;

	/**
	* Chart instance UUID
	*
	* @var string
	*/
	protected $uuid = null;

	/**
	* Collection of attributes
	*
	* @var array
	*/
	public $attributes = array();

	/**
	* Collection instance
	*
	* @var Hybrid\Chart\Fluent
	*/
	public $fluent;

	/**
	 * Construct a new instance
	 *
	 * <code>
	 * // just an example.
	 * $chart = new Laravie\Chartie\Foo();
	* </code>
	*
	* @access public
	* @param  Fluent    $fluent
	* @param  array     $attributes
	* @return void
	*/
	public function __construct(Fluent $fluent = null, array $attributes = array())
	{
		if (is_null($fluent)) $fluent = new Fluent;

		if ( ! empty($attributes)) $this->put($attributes);

		$this->attach($fluent);
		$this->uuid();
		$this->initiate();
	}

	/**
	 * Initiate the instance during construct.
	 *
	 * @abstract
	 * @access public
	 * @return void
	 */
	public abstract function initiate();

	/**
	 * Attach a fluent collection.
	 *
	 * @access public 	
	 * @param  Fluent   $fluent
	 * @return void
	 */
	public function attach(Fluent $fluent)
	{
		$this->fluent = $fluent;
	}

	/**
	 * Set chart attributes / configuration
	 *
	 * @access public
	 * @param  string   $name
	 * @param  mixed    $value
	 * @return self
	 */
	public function put($name, $value = null)
	{
		// Lets check if we're given a string for $name, in this case we
		// should expect there a second parameter available.
		if (is_string($name) and ! empty($name))
		{
			$name = array("{$name}" => $value);
		}

		// At this point, $name should always be an array, we should throw
		// an exception if it isn't.
		if ( ! is_array($name))
		{
			throw new InvalidArgumentException('Require [$name] to be set.');
		}

		// Lets assign all the things.
		foreach ($name as $key => $value)
		{
			$this->attributes[$key] = $value;
		}

		return $this;
	}

	/**
	 * Generate a UUID for the instance
	 *
	 * @access public
	 * @return string
	 */
	public function uuid()
	{
		if (is_null($this->uuid))
		{
			$this->uuid = $this->name.'_'.md5(mt_rand().time().microtime(true));
		}

		return $this->uuid;
	}

	/**
	 * Alias to self::render()
	 *
	 * @access public
	 * @see    render()
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 *
	 */
	public function __set($name, $value)
	{
		$this->attributes[$name] = $value;
	}

	/**
	 *
	 */
	public function __get($name)
	{
		return $this->attributes[$name];
	}

	/**
	 *
	 */
	public function __isset($name)
	{
		return isset($this->attributes[$name]);
	}

	/**
	 * Render the chart
	 *
	 * @access public
	 * @param  mixed    $width
	 * @param  mixed    $height
	 * @return string
	 */
	public function render()
	{
		$attributes = json_encode($this->attributes);
		$columns    = $this->fluent->get_columns();
		$rows       = $this->fluent->get_rows();
		$id         = $this->uuid();
		$name       = $this->name;

		return <<<SCRIPT
<div id="{$id}"></div>
<script>
google.load("visualization", "1", {packages:["corechart", "table", "geomap", "annotatedtimeline"]});
google.setOnLoadCallback(draw{$id});
function draw{$id} () {
var data, chart;
data = new google.visualization.DataTable();
{$columns}
{$rows}

chart = new google.visualization.{$name}(document.getElementById('{$id}'));
chart.draw(data, {$attributes});
};
</script>
SCRIPT;

	}
}