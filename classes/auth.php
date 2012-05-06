<?php

namespace Hybrid;

use \Auth as Laravel_Auth;
use \User_Role;

class Auth extends Laravel_Auth
{
	public static function roles()
	{
		$user       = static::user();
		$roles_id   = array();
		$user_id    = 0;

		if ( ! is_null($user))
		{
			$user_id = $user->id;

			$user_roles = User_Role::with('role')->where('user_id', '=', $user_id)->get();
			
			foreach ($user_roles as $role)
			{
				array_push($roles_id, $role->role->name);
			}
		}

		return $roles_id;
	}
}