<?php

return array(
	/*
	|----------------------------------------------------------------------
	| Submit Button String
	|----------------------------------------------------------------------
	|
	| Set default submit button for Hybrid\Form.
	|
	*/

	'submit_button'  => null,
	
	/*
	|----------------------------------------------------------------------
	| Layout
	|----------------------------------------------------------------------
	|
	| Hybrid\Form would require a View to parse the provided form instance.
	|
	*/

	'default_layout' => 'hybrid::form.horizontal',
	
	/*
	|----------------------------------------------------------------------
	| Layout Configuration
	|----------------------------------------------------------------------
	|
	| Set default submit button for Hybrid\Form.
	|
	*/

	'fieldset'       => array(
		'select'   => array('class' => 'span4'),
		'textarea' => array('class' => 'span4'),
		'input'    => array('class' => 'span4'),
		'password' => array('class' => 'span4'),
		'radio'    => array(),
		'checkbox' => array(),
	),
);