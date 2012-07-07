<?php namespace Hybrid;

use \Closure, View;

/**
 * Form class
 *
 * @package    Hybrid
 * @category   Response
 * @author     Laravel Hybrid Development Team
 */

class Form
{
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
		// Instantiate Form\Fieldset, this wrapper emulate form fieldset to 
		// create the form
		$this->fieldset = new Form\Fieldset;

		// run the form designer
		call_user_func($callback, $this->fieldset);
	}

	/**
	 * Name of form
	 *
	 * @var  string
	 */
	public $name = null;

	/**
	 * Form Fieldset instance
	 *
	 * @var  Form\Fieldset
	 */
	protected $fieldset = null;

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
	 * Return protected fieldset
	 * 
	 * @param  string       $key
	 * @return Form\Fieldset 
	 */
	public function __get($key)
	{
		if ($key === 'fieldset') return $this->fieldset;
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
	 * @param  Closure $callback
	 * @return void
	 */
	public function extend(Closure $callback)
	{
		// run the table designer
		call_user_func($callback, $this->fieldset);
	}

	/**
	 * Render the form
	 *
	 * @access  public
	 * @return  string
	 */
	public function render() {}
}