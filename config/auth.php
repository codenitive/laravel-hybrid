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
	|       \User_Role::with('role')->where('user_id', '=', $user_id)->get();
	|
	|       foreach ($user_roles as $role)
	|       {
	|           array_push($roles, $role->role->name);
	|       }
	|
	|       return $roles;
	|   }
	*/
	'roles' => function ($user_id, $roles)
	{
		if ( ! class_exists('User_Role', true)) return null;
		
		// in situation config is not a closure, we will use a basic convention structure.
		$user_roles = \User_Role::with('role')->where('user_id', '=', $user_id)->get();
		
		foreach ($user_roles as $role)
		{
			array_push($roles, $role->role->name);
		}

		return $roles;
	},

);