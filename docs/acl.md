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
		$acl->add_roles(array('admin', 'manager', 'editor', 'contributor'));
		
		// add actions
		$acl->add_action('post content');
		
		// same with role, you can add multiple actions
		$acl->add_actions(array('manage all', 'view content', 'edit account', 'post comment', 'moderate content'));
		
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

### make($name, $memory = null)

Creates a new instance of the Acl. 

	@param	String 					$name 		will default to 'default' if none is provided
	@param  Hybrid\Memory_Driver	$memory		need to be an instanceof Hybrid\Memory_Driver
	@return self
	
	$acl = Hybrid\Acl::make('client');
	
### register($name, $callback = null)

Register an Acl instance with Closure.

	@param	mixed		$name
	@param  Closure 	$callback
	@return self
	
	$acl = Hybrid\Acl::register('default', function ($acl)
	{
		// configure the default instance
	});
	
	// alternatively, you can skip first param for 'default'
	$acl = Hybrid\Acl::register(function ($acl)
	{
		// same as above
	});


### has_role($role)

Check if given role is available.

	@param 	String		$role
	@return bool
	
	$acl->has_role('admin'); // true

### add_role($role)

Add single user' role to the this instance.

	@param 	String			$role
	@throws AclException
	@return	self
	
	$acl->add_role('top-manager');

### add_roles($roles)

Add multiple user' roles to the this instance.

	@param	Array			$roles
	@throws AclException
	@return self
	
	$acl->add_roles(array('sys-admin', 'dept-admin'));

### has_action(String $action)

### add_action(String $action)

### add_actions(String|Array $actions)

### allow(String|Array $roles, String|Array $actions[, Boolean $type])

### deny(String|Array $roles, String|Array $actions)
