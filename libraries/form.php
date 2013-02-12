<?php namespace Hybrid;

/**
 * Form class
 *
 * @package    Hybrid
 * @category   Form
 * @author     Laravel Hybrid Development Team
 */


use \Closure, 
	\IoC,
	\Lang;

class Form {
	
	/**
	 * Set submit button message.
	 *
	 * @var string
	 */
	public static $submit_button = null;

	/**
	 * All of the registered form names.
	 *
	 * @var array
	 */
	protected static $names = array();

	/**
	 * Create a new Form instance
	 *
	 * <code>
	 *		// Create a new form instance
	 *		$view = Form::make(function ($form) {
	 *		
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
	 * Create a new Form instance
	 *
	 * @access  protected
	 * @param   Closure     $callback
	 * @return  void
	 */
	protected function __construct(Closure $callback)
	{
		// Instantiate Form\Grid
		$this->grid = new Form\Grid;

		if ( ! is_null(static::$submit_button)) 
		{
			$this->grid->submit_button = static::$submit_button;
		}

		// run the form designer
		call_user_func($callback, $this->grid);
	}

	/**
	 * Name of form
	 *
	 * @var  string
	 */
	public $name = null;

	/**
	 * Form Grid instance
	 *
	 * @var  Form\Grid
	 */
	protected $grid = null;

	/**
	 * Create a new form instance of a named form.
	 *
	 * <code>
	 *		// Create a new table instance
	 *		$view = Form::of('user-form', function ($form) {
	 *		
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
	 * @return Form\Grid 
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
	 * Extend Form designer 
	 *
	 * @access public
	 * @param  Closure  $callback
	 * @return void
	 */
	public function extend(Closure $callback)
	{
		// run the table designer
		call_user_func($callback, $this->grid);
	}

	/**
	 * Render the form
	 *
	 * @access  public
	 * @return  string
	 */
	public function render() 
	{
		// localize Grid instance.
		$grid        = $this->grid;
		$form_attr   = $grid->attr;

		// Build Form attribute, action and method should be unset from attr 
		// as it is build using Form::open()
		$form_method = $form_attr['method'];
		$form_action = $form_attr['action'];

		unset($form_attr['method']);
		unset($form_attr['action']);

		$submit_button = $grid->submit_button;

		if ( ! ($submit_button instanceof Lang))
		{
			$submit_button = Lang::line($submit_button);
		}

		// Build the view and render it.
		$view = IoC::resolve('hybrid.view', array($grid->view));

		return $view->with('token', $grid->token)
					->with('hiddens', $grid->hiddens)
					->with('row', $grid->row)
					->with('form_action', $form_action)
					->with('form_method', $form_method)
					->with('submit_button', $submit_button)
					->with('error_message', $grid->error_message)
					->with('form_attr', $form_attr)
					->with('fieldsets', $grid->fieldsets())
					->render();
	}
}