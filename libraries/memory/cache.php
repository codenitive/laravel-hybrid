<?php namespace Hybrid\Memory;

use Laravel\Cache as Laravel_Cache;

class Cache extends Driver {
	/**
	 * Storage name
	 * 
	 * @access  protected
	 * @var     string  
	 */
	protected $storage = 'cache';

	/**
	 * Load the data from database using Cache
	 *
	 * @access  public
	 * @return  void
	 */
	public function initiate() 
	{
		$this->name = isset($this->config['name']) ? $this->config['name'] : $this->name;
		
		$memories = Laravel_Cache::get('hybrid.memory.'.$this->name, array());

		foreach ($memories as $memory)
		{
			$value = unserialize($memory->value);

			$this->put($memory->name, $value);
		}
	}
	
	/**
	 * Add a shutdown event using Cache
	 *
	 * @access  public
	 * @return  void
	 */
	public function shutdown() 
	{
		$data = array();

		foreach ($this->data as $key => $value)
		{
			$serialize = serialize($value);

			array_push($data, (object) array(
				'name'  => $key,
				'value' => $serialize,
			));
		}

		Laravel_Cache::forever('hybrid.memory.'.$this->name, $data);
	}
}