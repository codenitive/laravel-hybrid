<?php namespace Hybrid\Form;

/**
 * Form class
 *
 * @package    Hybrid\Form
 * @category   Fieldset
 * @author     Laravel Hybrid Development Team
 */

use \Closure, \Config, \Input, Laravel\Form as Laravel_Form, Laravel\Fluent, \Str, 
	Hybrid\HTML,
	Hybrid\Exception;

class Fieldset 
{
	/**
	 * Fieldset name
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * Configurations
	 *
	 * @var  array
	 */
	protected $config = array();

	/**
	 * Fieldset HTML attributes
	 *
	 * @var array
	 */
	protected $attr = array();

	/**
	 * All the controls
	 *
	 * @var array
	 */
	protected $controls = array();

	/**
	 * Create a new Fieldset instance
	 *
	 * @access  public
	 * @return  void
	 */
	public function __construct($name, Closure $callback = null) 
	{
		if ($name instanceof Closure)
		{
			$callback = $name;
			$name     = null;
		}
		
		if ( ! empty($name)) $this->legend($name);

		// cached configuration option
		$this->config = Config::get('hybrid::form.fieldset');

		call_user_func($callback, $this);
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
	 * Append a new control to the table.
	 *
	 * <code>
	 *		// add a new control using just field name
	 *		$fieldset->control('input:text', 'username');
	 *
	 *		// add a new control using a label (header title) and field name
	 *		$fieldset->control('input:email', 'E-mail Address', 'email');
	 *
	 *		// add a new control by using a field name and closure
	 *		$fieldset->control('input:text', 'fullname', function ($control)
	 *		{
	 *			$control->label = 'User Name';
	 *			$control->field = function ($row) { 
	 * 				return $row->first_name.' '.$row->last_name; 
	 * 			};
	 *		});
	 * </code>
	 *
	 * @access  public			
	 * @param   mixed       $name
	 * @param   mixed       $callback
	 * @return  Fluent
	 */
	public function control($type, $name, $callback = null)
	{
		$control = null;
		$label   = $name;
		$config  = $this->config;

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
			$name    = Str::lower($name);
			$control = new Fluent(array(
				'id'      => $name,
				'name'    => $name,
				'value'   => null,
				'label'   => $label,
				'attr'    => array(),
				'options' => array(),
				'checked' => false,
				'field'   => null,
			));
		}

		// run closure
		if (is_callable($callback)) call_user_func($callback, $control);

		$field = function ($row, $control) use ($type, $config) {
			// prep control type information
			$type    = ($type === 'input:password' ? 'password' : $type);
			$methods = explode(':', $type);
			
			// set the name of the control
			$name    = $control->name;
			
			// set the value from old input, follow by row value.
			$value   = Input::old($name);

			if (! is_null($row->{$name}) and is_null($value)) $value = $row->{$name};

			// if the value is set from the closure, we should use it instead of 
			// value retrieved from attached data
			if ( ! is_null($control->value)) $value = $control->value;

			// should also check if it's a closure, when this happen run it.
			if ($value instanceof Closure) $value = $value($row, $control);

			switch (true)
			{
				case $type === 'select' :
					// set the value of options, if it's callable run it first
					$options = $control->options;
					
					if ($options instanceof Closure) $options = $options($row, $control);

					return Laravel_Form::select($name, $options, $value, HTML::pre_attributes($control->attr, $config['select']));
					break;

				case $type === 'checkbox' :
					return Laravel_Form::checkbox($name, $value, $control->checked);
					break;

				case $type === 'radio' :
					return Laravel_Form::radio($name, $value, $row->checked);
					break;

				case $type === 'textarea' :
					return Laravel_Form::textarea($name, $value, HTML::pre_attributes($control->attr, $config['textarea']));
					break;

				case $type === 'password' :
					return Laravel_Form::password($name, HTML::pre_attributes($control->attr, $config['password']));
					break;

				case (isset($methods[0]) and $methods[0] === 'input') :
					$methods[1] = $methods[1] ?: 'text';

					return Laravel_Form::input($methods[1], $name, $value, HTML::pre_attributes($control->attr, $config['input']));
					break;

				default :
					return Laravel_Form::input('text', $name, $value, HTML::pre_attributes($control->attr, $config['input']));
			}
		};

		if (is_null($control->field)) $control->field = $field;

		return $this->controls[] = $control;
	}

	/**
	 * Set Fieldset Legend name
	 *
	 * <code>
	 *     $fieldset->legend('User Information');
	 * </code>
	 * 
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function legend($name = null) 
	{
		if (is_null($name))
		{
			return $this->name;
		}

		$this->name = $name;
	}

	/**
	 * Magic Method for calling the methods.
	 */
	public function __call($method, array $arguments = array())
	{
		if (in_array($method, array('controls', 'view')))
		{
			return $this->$method;
		}
	}

	/**
	 * Magic Method for handling dynamic data access.
	 */
	public function __get($key)
	{
		if (in_array($key, array('attr', 'name', 'controls', 'view')))
		{
			return $this->{$key};
		}
	}

	/**
	 * Magic Method for handling the dynamic setting of data.
	 */
	public function __set($key, array $values)
	{
		if ( ! in_array($key, array('attr')))
		{
			throw new Exception(__METHOD__.": unable to set {$key}");
		}

		$this->attr($values, null);
	}

	/**
	 * Magic Method for checking dynamically-set data.
	 */
	public function __isset($key)
	{
		if (in_array($key, array('attr', 'name', 'controls', 'view')))
		{
			return isset($this->{$key});
		}
	}
}