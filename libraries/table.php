<?php namespace Hybrid;

use \Closure, \HTML, \Str;

class Table 
{
	/**
	 * All of the registered table names.
	 *
	 * @var array
	 */
	protected static $names = array();

	/**
	 * Create a new Table instance
	 *
	 * <code>
	 *		// Create a new table instance
	 *		$view = View::make('home.index');
	 * </code>
	 *
	 * @static
	 * @access  public
	 * @param   Closure     $callback
	 * @return  Table
	 */
	public static function make(Closure $callback)
	{
		return new static($callback);
	}

	/**
	 * Create a new Table instance
	 *
	 * @access  protected
	 * @param   Closure     $callback
	 * @param   string      $name
	 * @return  void
	 */
	protected function __construct(Closure $callback, $name = null)
	{
		// Set instance name when provided.
		if (is_string($name)) $this->name = $name;

		// Instantiate Table\Grid, this wrapper emulate 
		// table designer script to create the table
		$this->grid = new Table\Grid;

		// run the table designer
		call_user_func($callback, $this->grid);
	}

	/**
	 * Create a new table instance of a named table.
	 *
	 * @static
	 * @access   public
	 * @param    string	    $name
	 * @param    Closure	$callback
	 * @return   Table
	 */
	public static function of($name, Closure $callback)
	{
		if ( ! isset(static::$names[$name]))
		{
			static::$names[$name] = new static($callback, $name);
		}

		return static::$names[$name];
	}

	/**
	 * An alias to render()
	 *
	 * @access  public
	 * @see     render()
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Render the table
	 *
	 * @access  public
	 * @return  string
	 */
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