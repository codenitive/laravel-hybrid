<?php namespace Hybrid;

use \Auth as Laravel_Auth, \Config, \Event;

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

			$roles = Event::until('hybrid.auth.roles', array($user_id, $roles));
		}

		return $roles;
	}
}