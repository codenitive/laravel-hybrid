# Hybrid Acl

## Contents

- [Introduction](#introduction)
- [Methods](#methods)

<a name="introduction"></a>
## Introduction

The Acl class provides a standardized interface for authorization/priviledge in Fuel, using authentication from `Hybrid\Auth`.

### Examples

	Hybrid\Acl::register(function ($acl)
	{
		// add a single role
		$acl->add_role('subscriber');
		
		// add multiple roles
		$acl->add_roles(array('admin', 'manager'));
		
		// or
		$acl->add_roles('editor', 'contributor');
		
		// add actions
		$acl->add_action('post content');
		
		// same with role, you can add multiple actions
		$acl->add_actions(array('manage all', 'view content', 'edit account'));
		
		// or 
		$acl->add_actions('post comment', 'moderate content');
		
		/* now lets start building our ACL */
		
		// any roles can view content (even guest)
		$acl->allow('*', 'view content'); 
		
		// any roles except guest can edit account
		$acl->allow('!guest', 'edit account'); 
		
		// only admin can manage all
		$acl->allow('admin', 'manage all'); 
		
		// any roles can post content
		$acl->allow('*', 'post content'); 
		
		// oh wait, deny post content for guest and subscriber
		$acl->deny(array('subscriber', 'guest'), 'post content');
				
		return $acl;
	}); 
	
Given we are logged in as subscriber

	$acl = Hybrid\Acl::make();
	
	$acl->can('edit account'); // return true
	$acl->can('post content'); // return false

<a name="methods"></a>
## Methods

### make([String $name])

Creates a new instance of the Acl. 

	@param	String 	$name 	will default to 'default' if none is provided
	@return self
	
	$acl = Hybrid\Acl::make('client');
	
### register([String|Closure $name, Closure $callback])

### has_role(String $role)

### add_role(String $role)

### add_roles(String|Array $roles)

### has_action(String $action)

### add_action(String $action)

### add_actions(String|Array $actions)

### allow(String|Array $roles, String|Array $actions[, Boolean $type])

### deny(String|Array $roles, String|Array $actions)
