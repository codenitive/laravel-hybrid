<?php namespace Hybrid;

use \Closure, \HTML, \Str;

class Table 
{
	protected static $instances = array();

	public static function make($name = null, $callback = null)
	{
		if ($name instanceof Closure)
		{
			$callback = $name;
			$name     = null;
		}

		if (is_null($name)) $name = Str::random(50);

		if ( ! isset(static::$instances[$name]))
		{
			static::$instances[$name] = new static($name, $callback);
		}

		return static::$instances[$name];
	}

	protected function __construct($name, $callback)
	{
		if ( ! ($callback instanceof Closure))
		{
			throw new Exception(__METHOD__.": Excepted a closure but not given");
		}

		$this->name = $name;
		$this->grid = new Table\Grid;

		call_user_func($callback, $this->grid);
	}

	public function __toString()
	{
		return $this->render();
	}

	public function render()
	{	
		$table = '<table '.HTML::attributes($this->grid->attr).'>';

		$dataset = $this->grid->dataset();
		$columns = $this->grid->columns();

		if ($this->grid->structure === 'horizontal')
		{
			$head = '';
			$body = '';
			
			foreach ($columns as $key => $column)
			{
				$head .= '<th>'.$column->name.'</th>';
			}

			foreach ($dataset as $id => $data)
			{
				$body .= '<tr>';

				foreach ($columns as $key => $column)
				{
					$attr = $column->attr;

					if ($attr instanceof Closure) $attr = $attr($data);

					$body .= '<td '.HTML::attributes($attr).'>'.call_user_func($column->value, $data).'</td>';
				}

				$body .= '</tr>';
			}

			$table .= '<thead>';
			$table .= '<tr>'.$head.'</tr>';
			$table .= '</thead>';
			$table .= '<tbody>';
			$table .= $body;
			$table .= '</tbody>';
		}
		else
		{
			$body = '';
			
			foreach ($columns as $key => $column)
			{
				$body .= '<tr>';

				$body .= '<th>'.$column->name.'</th>';

				foreach ($dataset as $id => $data)
				{
					$attr = $column->attr;

					if ($attr instanceof Closure) $attr = $attr($data);
					
					$body .= '<td'.HTML::attributes($attr).'>'.call_user_func($column->value, $data).'</td>';
				}

				$body .= '</tr>';
			}

			$table .= '<tbody>';
			$table .= $body;
			$table .= '</tbody>';
		}

		$table .= '</table>';

		// create pagination
		if ($this->grid->paginate)
		{
			$table .= $this->grid->model->links();
		}

		return $table;
	}
}