<?php namespace Hybrid;

use \Closure, \HTML, \Str, \View;

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
		$columns = $this->grid->columns();
		$rows    = $this->grid->rows();

		$view = View::make($this->grid->view)
					->with('table_attr', $this->grid->table_attr)
					->with('row_attr', $this->grid->rows->attr)
					->with('row_empty', $this->grid->rows->empty)
					->with('columns', $columns)
					->with('rows', $rows);

		if ($this->grid->paginate) $view->with('pagination', $this->grid->model->links());

		return $view->render();
	}
}