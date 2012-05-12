<?php namespace Hybrid;

use \DB;

class Memory_Fluent extends Memory_Driver 
{
	/**
	 * @access  protected
	 * @var     string  storage configuration, currently only support runtime.
	 */
	protected $storage = 'fluent';

	protected $table   = null;

	protected $key_map = array();

	public function initiate() 
	{
		$this->table = isset($this->config['table']) ? $this->config['table'] : $this->name;
		
		$memories = DB::table($this->table)->get();

		foreach ($memories as $memory)
		{
			$value = unserialize($memory->value);

			$this->set($memory->name, $value);

			$this->key_map[$memory->name] = array(
				'id'       => $memory->id,
				'checksum' => md5($memory->value),
			);
		}
	}

	/**
	 * Add a shutdown event for DB engine
	 *
	 * @access  public
	 * @return  void
	 */
	public function shutdown() 
	{
		foreach ($this->data as $key => $value)
		{
			$is_new   = true;
			$id       = null;
			$checksum = '';
			
			if (array_key_exists($key, $this->key_map))
			{
				$is_new = false;
				extract($this->key_map[$key]);
			}

			$serialize = serialize($value);

			if ($checksum === md5($serialize))
			{
				continue;
			}

			$count = DB::table($this->table)->where('name', '=', $key)->count();

			if (true === $is_new and $count < 1)
			{
				DB::table($this->table)
					->insert(array(
						'name' => $key,
						'value' => $serialize,
					));
			}
			else
			{
				DB::table($this->table)
					->where('id', '=', $id)
					->update(array(
						'value' => $serialize,
					)); 
			}
		}
	}

}