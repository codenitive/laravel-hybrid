<?php

return array(

	/*
	|----------------------------------------------------------------------
	| Default Error Message String
	|----------------------------------------------------------------------
	|
	| Set default error message string format for Hybrid\Form.
	|
	*/

	'error_message' => '<p class="help-block error">:message</p>',

	/*
	|----------------------------------------------------------------------
	| Default Submit Button String
	|----------------------------------------------------------------------
	|
	| Set default submit button string or language replacement key for 
	| Hybrid\Form.
	|
	*/

	'submit_button'  => 'label.submit',
	
	/*
	|----------------------------------------------------------------------
	| Default View Layout
	|----------------------------------------------------------------------
	|
	| Hybrid\Form would require a View to parse the provided form instance.
	|
	*/

	'view' => 'hybrid::form.horizontal',
	
	/*
	|----------------------------------------------------------------------
	| Layout Configuration
	|----------------------------------------------------------------------
	|
	| Set default submit button for Hybrid\Form.
	|
	*/

	'fieldset' => array(
		'select'   => array('class' => 'span4'),
		'textarea' => array('class' => 'span4'),
		'input'    => array('class' => 'span4'),
		'password' => array('class' => 'span4'),
		'radio'    => array(),
		'checkbox' => array(),
	),
);