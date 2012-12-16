<?php namespace Hybrid\Acl;

use \Str,
	\Hybrid\InvalidArgumentException;

class Fluent {

	/**
	 * Collection name.
	 *
	 * @var string
	 */
	protected $name = null;
	
	/**
	 * Collection of this instance.
	 *
	 * @var array
	 */
	protected $collections = array();

	/**
	 * Construct a new instance.
	 *
	 * @access public	
	 * @param  string   $name
	 * @return void
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Get the collections.
	 *
	 * @access public
	 * @return array
	 */
	public function get()
	{
		return $this->collections;
	}

	/**
	 * Determine whether a key exists in collection
	 *
	 * @access public
	 * @param  string   $key
	 * @return boolean
	 */
	public function has($key)
	{
		$key = strval($key);
		$key = trim(Str::slug($key, '-'));

		return ( ! empty($key) and in_array($key, $this->collections));
	}

	/**
	 * Add multiple key to collection
	 *
	 * @access public
	 * @param  array   $keys
	 * @return void
	 */
	public function multiple_add(array $keys)
	{
		foreach ($keys as $key)
		{
			$this->add($key);
		}
	}

	/**
	 * Add a key to collection
	 *
	 * @access public
	 * @param  string   $key
	 * @return void
	 */
	public function add($key)
	{
		if (is_null($key)) 
		{
			throw new InvalidArgumentException("Can't add NULL {$this->name}.");
		}

		$key = trim(Str::slug($key, '-'));

		if ( ! $this->has($key))
		{
			array_push($this->collections, $key);
		}
	}

	/**
	 * Remove a key from collection
	 *
	 * @access public
	 * @param  string   $key
	 * @return void
	 */
	public function remove($key)
	{
		if (is_null($key)) 
		{
			throw new InvalidArgumentException("Can't add NULL {$this->name}.");
		}

		$key = trim(Str::slug($key, '-'));

		if ($this->has($key))
		{
			$array_key = array_search($this->collections, $key);

			unset($this->collection[$array_key]);
		}
	}

	/**
	 * Magic method for __toString()
	 */
	public function __toString()
	{
		return $this->get();
	}
}