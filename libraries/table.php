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
	 *		$view = Table::make(function ($table) {
	 *			$table->with(User::all());
	 *
	 *			$table->column('username');
	 *			$table->column('password');
	 * 		});
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
	 * @return  void
	 */
	protected function __construct(Closure $callback)
	{
		// Instantiate Table\Grid, this wrapper emulate 
		// table designer script to create the table
		$this->grid = new Table\Grid;

		// run the table designer
		call_user_func($callback, $this->grid);
	}

	/**
	 * Name of table
	 *
	 * @var  string
	 */
	public $name = null;
	/**
	 * Table Grid instance
	 *
	 * @var  Table\Grid
	 */
	protected $grid = null;

	/**
	 * Create a new table instance of a named table.
	 *
	 * <code>
	 *		// Create a new table instance
	 *		$view = Table::of('user-table', function ($table) {
	 *			$table->with(User::all());
	 *
	 *			$table->column('username');
	 *			$table->column('password');
	 * 		});
	 * </code>
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
			static::$names[$name]       = new static($callback, $name);
			static::$names[$name]->name = $name;
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

		$view->with('pagination', (true === $this->grid->paginate ? $this->grid->model->links() : ''));

		return $view->render();
	}
}