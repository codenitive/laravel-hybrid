<?php namespace Hybrid;

/**
 * Auth class
 *
 * @package    Hybrid
 * @category   Auth
 * @author     Laravel Hybrid Development Team
 */

use \Auth as Laravel_Auth, \Event;

class Auth extends Laravel_Auth {
	
	/**
	 * Cached user to roles relationship
	 * 
	 * @var array
	 */
	protected static $user_roles = null;
	
	/**
	 * Get the current user's roles of the application.
	 *
	 * If the user is a guest, empty array should be returned.
	 *
	 * @static
	 * @access  public
	 * @return  array
	 */
	public static function roles()
	{
		$user    = static::user();
		$roles   = array();
		$user_id = 0;

		// only search for roles when user is logged
		if ( ! is_null($user)) $user_id = $user->id;

		if (is_null(static::$user_roles[$user_id]))
		{
			static::$user_roles[$user_id] = Event::until('hybrid.auth.roles', array($user, $roles));
		}

		return static::$user_roles[$user_id];
	}

	/**
	 * Determine if current user has the given role
	 *
	 * @static
	 * @access public
	 * @param  string   $role
	 * @return boolean
	 */
	public static function is($role)
	{
		$roles = static::roles();

		return in_array($role, $roles);
	}
}