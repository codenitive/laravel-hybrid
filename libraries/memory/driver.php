<?php namespace Hybrid\Memory;

abstract class Driver
{
	protected $name = null;

	protected $config = array();

	/**
	 * @access  protected
	 * @var     array   collection of key-value pair of either configuration or data
	 */
	protected $data = array();

	/**
	 * @access  protected
	 * @var     string  storage configuration, currently only support runtime.
	 */
	protected $storage;

	/**
	 * Construct an instance.
	 *
	 * @access  public
	 * @param   string  $storage    set storage configuration (default to 'runtime').
	 */
	public function __construct($name = 'default', $config = array()) 
	{
		$this->name   = $name;
		$this->config = is_array($config) ? $config : array(); 

		$this->initiate();
	}

	/**
	 * Get value of a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to search.
	 * @param   mixed   $default    Default value if key doesn't exist.
	 * @return  mixed
	 */
	public function get($key = null, $default = null)
	{
		return array_get($this->data, $key, $default);
	}

	/**
	 * Set a value from a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to add the value.
	 * @param   mixed   $value      The value.
	 * @return  void
	 */
	public function put($key, $value = '')
	{
		array_set($this->data, $key, $value);

		return $this;
	}

	/**
	 * Delete value of a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to delete.
	 * @return  bool
	 */
	public function forget($key = null)
	{
		return array_forget($this->data, $key);
	}

	public abstract function initiate();
	
	public abstract function shutdown();

}