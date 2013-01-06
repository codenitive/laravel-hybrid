<?php namespace Hybrid\Form;

/**
 * Form class
 *
 * @package    Hybrid\Form
 * @category   Grid
 * @author     Laravel Hybrid Development Team
 */

use \Closure, 
	Laravel\Fluent, 
	Laravel\Form as F,
	Hybrid\Exception;

class Grid {

	/**
	 * Enable CSRF token
	 *
	 * @var boolean
	 */
	public $token = false;

	/**
	 * Hidden fields
	 *
	 * @var array
	 */
	protected $hiddens = array();

	/**
	 * List of row in array
	 *
	 * @var array
	 */
	protected $row = null;

	/**
	 * All the fieldsets
	 *
	 * @var array
	 */
	protected $fieldsets = array();

	/**
	 * Form HTML attributes
	 *
	 * @var array
	 */
	protected $attr = array();

	/**
	 * Set submit button message.
	 *
	 * @var string
	 */
	public $submit_button = 'label.submit';

	/**
	 * Set the no record message
	 *
	 * @var string
	 */
	public $error_message = '<p class="help-block error">:message</p>';

	/**
	 * Selected view path for form layout
	 *
	 * @var array
	 */
	protected $view = 'hybrid::form.horizontal';

	/**
	 * Create a new Grid instance
	 *
	 * @access  public
	 * @return  void
	 */
	public function __construct()
	{
		$this->row = array();
	}

	/**
	 * Set fieldset layout (view)
	 *
	 * <code>
	 *		// use default horizontal layout
	 *		$fieldset->layout('horizontal');
	 *
	 * 		// use default vertical layout
	 * 		$fieldset->layout('vertical');
	 *
	 *		// define fieldset using custom view
	 *		$fieldset->layout('path.to.view');
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
				$this->view = "hybrid::form.{$name}";
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
	public function row($row = null)
	{
		if (is_null($row)) return $this->row;

		$this->row = $row;
	}

	/**
	 * Add or append fieldset HTML attributes
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
	 * Create a new Fieldset instance
	 *
	 * @access  public
	 * @return  Form\Fieldset
	 */
	public function fieldset($name, Closure $callback = null)
	{
		return $this->fieldsets[] = new Fieldset($name, $callback);
	}

	/**
	 * Add hidden field.
	 *
	 * @access public
	 * @param  string   $name
	 * @param  Closure  $callback
	 * @return void
	 */
	public function hidden($name, $callback = null)
	{
		$value = null;
		
		if (isset($this->row) and isset($this->row->{$name})) 
		{
			$value = $this->row->{$name};
		}

		$field = new Fluent(array(
			'name'  => $name,
			'value' => $value ?: '',
			'attr'  => array(),
		));

		if ($callback instanceof Closure) call_user_func($callback, $field);

		$this->hiddens[$name] = F::hidden($name, $field->value, $field->attr);
	}

	/**
	 * Magic Method for calling the methods.
	 */
	public function __call($method, array $arguments = array())
	{
		unset($arguments);

		if (in_array($method, array('fieldsets', 'view', 'hiddens')))
		{
			return $this->$method;
		}
	}

	/**
	 * Magic Method for handling dynamic data access.
	 */
	public function __get($key)
	{
		if ( ! in_array($key, array('attr', 'row', 'view', 'hiddens')))
		{
			throw new Exception(__CLASS__.": unable to use __get for {$key}");
		}

		return $this->{$key};
	}

	/**
	 * Magic Method for handling the dynamic setting of data.
	 */
	public function __set($key, array $arguments)
	{
		if ( ! in_array($key, array('attr')))
		{
			throw new Exception(__METHOD__.": unable to set {$key}");
		}

		$this->attr($arguments, null);
	}

	/**
	 * Magic Method for checking dynamically-set data.
	 */
	public function __isset($key)
	{
		if ( ! in_array($key, array('attr', 'row', 'view', 'hiddens')))
		{
			throw new Exception(__CLASS__.": unable to use __isset for {$key}");
		}

		return isset($this->{$key});
	}
}