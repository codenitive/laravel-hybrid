# Hybrid Acl

## Contents

- [Introduction](#introduction)
- [Methods](#methods)

<a name="introduction"></a>
## Introduction

The Acl class provides a standardized interface for authorization/priviledge in Laravel, using authentication from `Hybrid\Auth`.

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

But wait, how does `Hybrid\Acl` know which roles does the current user belongs to? For this please refer to [`Hybrid\Auth` documentation](/bundocs/hybrid/classes/auth).

<a name="methods"></a>
## Methods

### make($name, $memory = null)

Creates a new instance of the Acl. 

	@static
	@param	String 					$name 		will default to 'default' if none is provided
	@param  Hybrid\Memory\Driver	$memory		need to be an instanceof Hybrid\Memory\Driver
	@return self
	
	$acl = Hybrid\Acl::make('client');
	
### register($name, $callback = null)

Register an Acl instance with Closure.

	@static
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

### with($memory)

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

### has_action($action)

Check if given action is available.

	@param 	String		$action
	@return bool
	
	$acl->has_action('post content'); // true

### add_action($action)

Add single action to this instance.

	@param	String			$action
	@throws AclException
	@return self
	
	$acl->add_action('export content');


### add_actions(String|Array $actions)

Add multiple actions to this instance.

	@param	Array			$actions
	@throws AclException
	@return self
	
	$acl->add_actions(array('edit content', 'delete content'));

### allow($roles, $actions, $allow = true)

Assign single or multiple `$roles` + `$actions` to have access.

	@param  mixed   $roles          A string or an array of roles
	@param  mixed   $actions        A string or an array of action name
	@param  bool    $allow
	@return self
	
	$acl->allow('dept-admin', 'delete content');

### deny($roles, $actions)

Shorthand function to deny access for single or multiple `$roles` and `$actions`

	@param  mixed   $roles          A string or an array of roles
	@param  mixed   $actions        A string or an array of action name
	@return self
	
	$acl->deny('top-manager', 'post content');
	
### can($action)

Verify whether current user has sufficient roles to access the actions based on available type of access.

	@param  mixed   $action     A string of action name
	@return bool
	@throws AclException
	
	// as a dept-admin
	$acl->can('delete content');	// true
	$acl->can('post content');		// false