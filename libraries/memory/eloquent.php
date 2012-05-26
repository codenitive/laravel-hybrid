<?php namespace Hybrid\Memory;

class Eloquent extends Driver 
{
	/**
	 * @access  protected
	 * @var     string  storage configuration, currently only support runtime.
	 */
	protected $storage = 'eloquent';

	protected $key_map = array();

	public function initiate() 
	{
		$this->name = isset($this->config['name']) ? $this->config['name'] : $this->name;
		
		$memories = call_user_func(array($this->name, 'all'));

		foreach ($memories as $memory)
		{
			$value = unserialize($memory->value);

			$this->put($memory->name, $value);

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

			$count = call_user_func(array($this->name, 'where'), 'name', '=', $key)->count();

			if (true === $is_new and $count < 1)
			{
				call_user_func(array($this->name, 'create'), array(
					'name' => $key,
					'value' => $serialize,
				));
			}
			else
			{
				$memory        = call_user_func(array($this->name, 'find'), $id);
				$memory->value = $serialize;

				$memory->save();
			}
		}
	}

}