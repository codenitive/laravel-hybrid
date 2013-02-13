<?php namespace Hybrid;

/**
 * Form class
 *
 * @package    Hybrid
 * @category   Form
 * @author     Laravel Hybrid Development Team
 */


use \Closure, 
	\Config,
	\IoC,
	\Lang;

class Form {

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
		$this->grid = new Form\Grid(Config::get('hybrid::form'));

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
			static::$names[$name] = new static($callback);

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
		$grid      = $this->grid;
		$attributes = $grid->markup;

		// Build Form attribute, action and method should be unset from attr 
		// as it is build using Form::open()
		$form_method = $attributes['method'];
		$form_action = $attributes['action'];

		unset($attributes['method']);
		unset($attributes['action']);

		$submit_button = $grid->submit_button;

		if ( ! ($submit_button instanceof Lang))
		{
			$submit_button = Lang::line($submit_button);
		}

		$data = array(
			'token'         => $grid->token,
			'hiddens'       => $grid->hiddens,
			'row'           => $grid->row,
			'form_action'   => $form_action,
			'form_method'   => $form_method,
			'submit_button' => $submit_button,
			'error_message' => $grid->error_message,
			'markup'        => $attributes,
			'fieldsets'     => $grid->fieldsets(),
		);

		// Build the view and render it.
		return IoC::resolve('hybrid.view', array($grid->view))
					->with($data)
					->render();
	}
}