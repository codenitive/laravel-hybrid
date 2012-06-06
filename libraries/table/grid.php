<?php namespace Hybrid\Table;

use \Closure, Laravel\Fluent, \Str, 
	Hybrid\Exception;

class Grid 
{
	protected $model = null;

	protected $attr = array();

	protected $columns = array();

	protected $paginate = false;

	protected $structure = 'horizontal';

	public function __contruct() {}

	public function with($model, $paginate = false)
	{
		$this->model    = $model;
		$this->paginate = $paginate;
	}

	public function column($name, Closure $callback = null)
	{
		$column = null;

		if (is_string($name))
		{
			$name  = Str::lower($name);
			$value = function ($row) use ($name) {
				return $row->{$name};
			};

			$column = new Fluent(array(
				'id'    => $name,
				'name'  => Str::title($name),
				'value' => $value,
			));
		}

		if ( ! is_null($callback))
		{
			call_user_func($callback, $column);
		}

		return $this->columns[] = $column;
	}

	public function attr(array $key = null)
	{
		if (is_null($key)) return $this->attr;

		$this->attr = array_merge($this->attr, $key);
	}

	public function __call($method, array $arguments)
	{
		if (in_array($method, array('columns', 'structure')))
		{
			return $this->$method;
		}

		if (in_array($method, array('vertical', 'horizontal')))
		{
			return $this->structure = $method;
		}
	}

	public function dataset()
	{
		if ($this->paginate) return $this->model->results;

		return $this->model;
	}

	public function __get($key)
	{
		if (in_array($key, array('attr', 'columns', 'model', 'paginate', 'structure')))
		{
			return $this->{$key};
		}
	}

	public function __set($key, array $values)
	{
		if ($key !== 'attr')
		{
			throw new Exception(__METHOD__.": unable to set {$key}");
		}

		$this->attr($values);
	}
}