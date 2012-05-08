<?php namespace Hybrid;

use \Auth as Laravel_Auth, Config;

class Auth extends Laravel_Auth
{
	/**
	 * Get the current user's roles of the application.
	 *
	 * If the user is a guest, null should be returned.
	 *
	 * @return array
	 */
	public static function roles()
	{
		$user    = static::user();
		$roles   = array();
		$user_id = 0;

		// only search for roles when user is logged
		if ( ! is_null($user))
		{
			$user_id = $user->id;

			// get associated roles from configuration.
			if ( ! (($callback = Config::get('hybrid::auth.roles')) instanceof Closure))
			{
				$callback = function($id, $roles)
				{
					// in situation config is not a closure, we will use a basic convention structure.
					$user_roles = \User_Role::with('role')->where('user_id', '=', $id)->get();
					
					foreach ($user_roles as $role)
					{
						array_push($roles, $role->role->name);
					}

					return $roles;
				}
			}

			$roles = $callback($user_id, $roles);
		}

		return $roles;
	}
}