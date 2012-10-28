<?php namespace Hybrid\Table;

/**
 * Table class
 *
 * Table Generator Class inspired by Squi Bundle for Laravel 
 *
 * @package    Hybrid\Table
 * @category   Grid
 * @author     Laravel Hybrid Development Team
 * @author     Kelly Banman <https://github.com/kbanman>
 * @link       https://github.com/kbanman/laravel-squi
 */

use \Closure, Laravel\Fluent, \Str, 
	Hybrid\Exception;

class Grid 
{
	/**
	 * Set the no record message
	 *
	 * @var string
	 */
	public $empty_message = 'message.no-record';

	/**
	 * List of rows in array, is used when model is null
	 *
	 * @var array
	 */
	protected $rows = null;

	/**
	 * Eloquent model used for table
	 *
	 * @var mixed
	 */
	protected $model = null;

	/**
	 * Table HTML attributes
	 *
	 * @var array
	 */
	protected $attr = array();

	/**
	 * All the columns
	 *
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Enable to attach pagination during rendering
	 *
	 * @var bool
	 */
	protected $paginate = false;

	/**
	 * Selected view path for table layout
	 *
	 * @var array
	 */
	protected $view = 'hybrid::table.horizontal';

	/**
	 * Create a new Grid instance
	 *
	 * @access  public
	 * @return  void
	 */
	public function __construct() 
	{
		$this->empty_message = __($this->empty_message, null, 'No records');

		$this->rows = new Fluent(array(
			'data' => array(),
			'attr' => function ($row) { return array(); },
		));
	}

	/**
	 * Attach Eloquent results as row and allow pagination (if required)
	 *
	 * <code>
	 *		// add model without pagination
	 *		$table->with(User::all());
	 *
	 *		// add model with pagination
	 *		$table->with(User::paginate(30), true);
	 * </code>
	 *
	 * @access  public		
	 * @param   Eloquent    $model
	 * @param   bool        $paginate
	 * @return  void
	 */
	public function with($model, $paginate = false)
	{
		$this->paginate = $paginate;
		$this->model    = $model;
		$this->rows(true === $paginate ? $model->results : $model);
	}

	/**
	 * Set table layout (view)
	 *
	 * <code>
	 *		// use default horizontal layout
	 *		$table->layout('horizontal');
	 *
	 * 		// use default vertical layout
	 * 		$table->layout('vertical');
	 *
	 *		// define table using custom view
	 *		$table->layout('path.to.view');
	 * </code>
	 *
	 * @access  public
	 * @param   string      $name
	 * @return  void
	 */
	public function layout($name)
	{
		switch ($name)
		{
			case 'horizontal' :
			case 'vertical' :
				$this->view = "hybrid::table.{$name}";
				break;
			default :
				$this->view = $name;
				break;
		}
	}

	/**
	 * Attach rows data instead of assigning a model
	 *
	 * <code>
	 *		// assign a data
	 * 		$table->rows(DB::table('users')->get());
	 * </code>
	 *
	 * @access  public		
	 * @param   array       $rows
	 * @return  void
	 */
	public function rows(array $rows = null)
	{
		if (is_null($rows)) return $this->rows->data;

		$this->rows->data = $rows;
	}

	/**
	 * Append a new column to the table.
	 *
	 * <code>
	 *		// add a new column using just field name
	 *		$table->column('username');
	 *
	 *		// add a new column using a label (header title) and field name
	 *		$table->column('User Name', 'username');
	 *
	 *		// add a new column by using a field name and closure
	 *		$table->column('fullname', function ($column)
	 *		{
	 *			$column->heading = 'User Name';
	 *			$column->value   = function ($row) { 
	 * 				return $row->first_name.' '.$row->last_name; 
	 * 			};
	 *
	 * 			$column->heading_attr(array('class' => 'header-class'));
	 *
	 * 			$column->cell_attr(function ($row) { 
	 *				return array('data-id' => $row->id);
	 *			});
	 *		});
	 * </code>
	 *
	 * @access  public			
	 * @param   mixed       $name
	 * @param   mixed       $callback
	 * @return  Fluent
	 */
	public function column($name, $callback = null)
	{
		$column = null;
		$label  = $name;

		switch (true)
		{
			case ! is_string($label) :
				$callback = $label;
				$label    = null;
				$name     = null;
				break;

			case is_string($callback) :
				$name     = $callback;
				$callback = null;
				break;
				
			default :
				$name  = Str::lower($name);
				$label = Str::title($name);
				break;
		}

		// populate the column when label is a string
		if (is_string($label))
		{
			$name   = Str::lower($name);
			$value  = function ($row) use ($name) { return $row->{$name}; };
			$column = new Fluent(array(
				'id'         => $name,
				'label'      => $label,
				'value'      => $value,
				'label_attr' => array(),
				'cell_attr'  => function ($row) { return array(); },
			));
		}

		// run closure
		if (is_callable($callback)) call_user_func($callback, $column);

		return $this->columns[] = $column;
	}

	/**
	 * Add or append table HTML attributes
	 *
	 * @access  public
	 * @param   mixed       $key
	 * @param   mixed       $value
	 * @return  void
	 */
	public function attr($key = null, $value = null)
	{
		switch (true)
		{
			case is_null($key) :
				return $this->attr;
				break;

			case is_array($key) :
				$this->attr = array_merge($this->attr, $key);
				break;

			default :
				$this->attr[$key] = $value;
				break;
		}
	}

	/**
	 * Magic Method for calling the methods.
	 */
	public function __call($method, array $arguments = array())
	{
		if ( ! in_array($method, array('columns', 'view')))
		{
			throw new Exception(__CLASS__.": unable to use __call for {$method}");
		}

		return $this->$method;
	}

	/**
	 * Magic Method for handling dynamic data access.
	 */
	public function __get($key)
	{
		if ( ! in_array($key, array('attr', 'columns', 'model', 'paginate', 'view', 'rows')))
		{
			throw new Exception(__CLASS__.": unable to use __get for {$key}");
		}
		
		return $this->{$key};
	}

	/**
	 * Magic Method for handling the dynamic setting of data.
	 */
	public function __set($key, array $values)
	{
		if ( ! in_array($key, array('attr')))
		{
			throw new Exception(__CLASS__.": unable to use __set for {$key}");
		}

		$this->attr($values, null);
	}

	/**
	 * Magic Method for checking dynamically-set data.
	 */
	public function __isset($key)
	{
		if ( ! in_array($key, array('attr', 'columns', 'model', 'paginate', 'view')))
		{
			throw new Exception(__CLASS__.": unable to use __isset for {$key}");
		}

		return isset($this->{$key});
	}
}