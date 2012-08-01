<?php

return array(
	/*
	|--------------------------------------------------------------------------
	| Retrieve Role associated to current user
	|--------------------------------------------------------------------------
	|
	| Laravel Hybrid would need to know the list of roles (name) associated to 
	| the current user, based on roles defined in `\Hybrid\Acl::add_role()`.
	|
	| Example:
	|
	|   'roles' => function ($user, $roles)
	|   {
	|       if ( ! is_null($user)) return;
	|
	|       // in situation config is not a closure, we will use a basic
	|       // convention structure.
	|       $user_roles = \Role_User::with('roles')
	|          ->where('user_id', '=', $user->id)->get();
	|
	|       foreach ($user_roles as $role)
	|       {
	|           array_push($roles, $role->roles->name);
	|       }
	|
	|       return $roles;
	|   }
	*/
	'roles' => function ($user, $roles)
	{
		if ( ! is_null($user)) return;
		
		// This is with the assumption that Eloquent model already setup to 
		// use pivot table between User and Role Model.
		foreach ($user->roles()->get() as $role)
		{
			array_push($roles, $role->name);
		}

		return $roles;
	},

);