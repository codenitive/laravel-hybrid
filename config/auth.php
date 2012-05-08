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
	|       \User_Role::with('role')->where('user_id', '=', $id)->get();
	|
	|       foreach ($user_roles as $role)
	|       {
	|           array_push($roles, $role->role->name);
	|       }
	|
	|       return $roles;
	|   }
	*/
	'roles' => null,

);