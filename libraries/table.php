<?php namespace Hybrid;

/**
 * Table class
 *
 * Table Generator Class inspired by Squi Bundle for Laravel 
 *
 * @package    Hybrid
 * @category   Table
 * @author     Laravel Hybrid Development Team
 * @author     Kelly Banman <https://github.com/kbanman>
 * @link       https://github.com/kbanman/laravel-squi
 */

use \Closure, 
	\Input, 
	\IoC,
	\Lang;

class Table {
	
	/**
	 * Set the no record message
	 *
	 * @var string
	 */
	public static $empty_message = null;
	
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
		// Instantiate Table\Grid, this wrapper emulate table designer script 
		// to create the table
		$this->grid = new Table\Grid;

		if ( ! is_null(static::$empty_message)) 
		{
			$this->grid->empty_message = static::$empty_message;
		}

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
	public static function of($name, Closure $callback = null)
	{
		if ( ! isset(static::$names[$name]))
		{
			static::$names[$name]       = new static($callback);
			static::$names[$name]->name = $name;
		}

		return static::$names[$name];
	}

	/**
	 * Return protected grid
	 *
	 * @access public
	 * @param  string       $key
	 * @return Table\Grid 
	 */
	public function __get($key)
	{
		if ($key === 'grid') return $this->grid;
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
	 * Extend Table designer 
	 *
	 * @access public
	 * @param  Closure $callback
	 * @return void
	 */
	public function extend(Closure $callback)
	{
		// run the table designer
		call_user_func($callback, $this->grid);
	}

	/**
	 * Render the table
	 *
	 * @access  public
	 * @return  string
	 */
	public function render()
	{
		// localize Table\Grid object
		$grid     = $this->grid;
		
		// Add paginate value for current listing while appending query string
		$input    = Input::query();

		// we shouldn't append ?page
		if (isset($input['page'])) unset($input['page']);

		$paginate = (true === $grid->paginate ? $grid->model->appends($input)->links() : '');

		$empty_message = $grid->empty_message;

		if ( ! ($empty_message instanceof Lang))
		{
			$empty_message = Lang::line($empty_message);
		}

		// Build the view and render it.
		$view = IoC::resolve('hybrid.view', array($grid->view));

		return $view->with('table_attr', $grid->attr)
					->with('row_attr', $grid->rows->attr)
					->with('empty_message', $empty_message)
					->with('columns', $grid->columns())
					->with('rows', $grid->rows())
					->with('pagination', $paginate)
					->render();
	}
}