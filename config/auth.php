<?php

return array(
	/*
	|--------------------------------------------------------------------------
	| Retrieve Role associated to current user
	|--------------------------------------------------------------------------
	|
	| Laravel Hybrid would need to know the list of roles (name) associated to 
	| the current user, in relevant to all role defined in `\Hybrid\Acl::add_role()`.
	|
	| Example:
	|
	|   'roles' => function ($id, $roles)
	|   {
	|       if ( ! class_exists('Role_User', true)) return null;
	|
	|       // in situation config is not a closure, we will use a basic convention structure.
	|       $user_roles = \Role_User::with('roles')->where('user_id', '=', $user_id)->get();
	|
	|       foreach ($user_roles as $role)
	|       {
	|           array_push($roles, $role->roles->name);
	|       }
	|
	|       return $roles;
	|   }
	*/
	'roles' => function ($user_id, $roles)
	{
		if ( ! class_exists('User', true)) return null;
		
		// This is with the assumption that Eloquent model already setup to use pivot table
		// between User and Role Model.
		$user = \User::with('roles')->find($user_id);
		
		foreach ($user->roles as $role) array_push($roles, $role->name);

		return $roles;
	},

);