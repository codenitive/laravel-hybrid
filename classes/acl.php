<?php namespace Hybrid;

use \Closure, \Str;

class AclException extends \Exception {}

class Acl
{
	protected static $initiated = false;

	/**
	 * Cache ACL instance so we can reuse it on multiple request. 
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $instances = array();

	/**
	 * Initiate a new Acl instance.
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name
	 * @return  Acl
	 */
	public static function make($name = null)
	{
		if (is_null($name)) $name = 'default';

		if ( ! isset(static::$instances[$name])) static::$instances[$name] = new static($name);

		return static::$instances[$name];
	}

	/**
	 * Register an Acl instance.
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name
	 * @param   Closure $callback
	 * @return  Acl
	 */
	public static function register($name, $callback = null)
	{
		if ($name instanceof Closure)
		{
			$callback = $name;
			$name     = null;
		}

		$instance = static::make($name);

		$callback($instance);
	}

	/**
	 * Construct a new object.
	 *
	 * @access  protected
	 */
	protected function __construct($name = null) 
	{
		$this->name = $name;
	}

	protected $name = null;

	/**
	 * List of roles
	 * 
	 * @access  protected
	 * @var     array
	 */
	protected $roles = array('guest');
	 
	/**
	 * List of actions
	 * 
	 * @access  protected
	 * @var     array
	 */
	protected $actions = array();
	 
	/**
	 * List of ACL map between roles, action
	 * 
	 * @access  protected
	 * @var     array
	 */
	protected $acl = array();

	/**
	 * Check if given role is available
	 *
	 * @access  public
	 * @param   string  $role
	 * @return  bool
	 */
	public function has_role($role)
	{
		$role = strval($role);

		if ( ! empty($role) and in_array($role, $this->roles)) return true;

		return false;
	}

	/**
	 * Add new user role(s) to the this instance
	 * 
	 * @access  public
	 * @param   mixed   $roles      A string or an array of roles
	 * @return  Acl                 chaining
	 * @throws  AclException
	 */
	public function add_roles($roles = null)
	{
		if (is_string($roles)) $roles = func_get_args();
		
		if (is_array($roles)) 
		{
			foreach ($roles as $role)
			{
				try
				{
					$this->add_role($role);
				}
				catch (AclException $e)
				{
					continue;
				}
			}
		}

		return $this;
	}

	/**
	 * Add new user role to the this instance
	 * 
	 * @access  public
	 * @param   mixed   $role       A string or an array of roles
	 * @return  Acl                 chaining
	 * @throws  AclException
	 */
	public function add_role($role)
	{
		if (is_null($role)) 
		{
			throw new AclException(__METHOD__.": Can't add NULL role.");
		}

		$role = trim(Str::slug($role, '-'));

		if ( ! $this->has_role($role))
		{
			throw new AclException(__METHOD__.": Role {$role} already exist.");
		}

		array_push($this->roles, $role);

		return $this;
	}

	/**
	 * Check if given action is available
	 *
	 * @access  public
	 * @param   string  $action
	 * @return  bool
	 */
	public function has_action($action)
	{
		$action = strval($action);

		if ( ! empty($action) and in_array($action, $this->actions)) return true;

		return false;
	}

	/**
	 * Add new action(s) to this instance
	 * 
	 * @access  public
	 * @param   mixed   $actions    A string of action name
	 * @return  Acl                 chaining
	 * @throws  AclException
	 */
	public function add_actions($actions = null) 
	{
		if (is_string($actions)) $actions = func_get_args();
		
		if (is_array($actions)) 
		{
			foreach ($actions as $action => $callback)
			{
				if (is_numeric($action))
				{
					$action   = $callback;
					$callback = null;
				}

				try
				{
					$this->add_action($action, $callback);
				}
				catch (AclException $e)
				{
					continue;
				}
			}
		}

		return $this;
	}

	/**
	 * Add new action to this instance
	 * 
	 * @access  public
	 * @param   mixed   $actions    A string of action name
	 * @return  Acl                 chaining
	 * @throws  AclException
	 */
	public function add_action($action, $callback = null) 
	{
		if (is_null($action)) 
		{
			throw new AclException(__METHOD__.": Can't add NULL actions.");
		}

		$action = trim(Str::slug($action, '-'));
		
		if ($this->has_action($action))
		{
			throw new AclException(__METHOD__.": Action {$action} already exist.");
		}

		array_push($this->actions, $action);

		return $this;
	}

	/**
	 * Verify whether current user has sufficient roles to access the actions based 
	 * on available type of access.
	 *
	 * @access  public
	 * @param   mixed   $action     A string of action name
	 * @param   string  $type       need to be any one of deny, view, create, edit, delete or all
	 * @return  bool
	 * @throws  AclException
	 */
	public function can($action) 
	{
		$roles = array();

		if ( ! in_array(Str::slug($action, '-'), $this->actions)) 
		{
			throw new AclException(__METHOD__.": Unable to verify unknown action {$action}.");
		}

		if (is_null(Auth::user()))
		{
			// only add guest if it's available
			if (in_array('guest', $this->roles)) array_push($roles, 'guest');
		}
		else $roles = Auth::roles();

		$action = Str::slug($action, '-');

		foreach ($roles as $role) 
		{
			$role = Str::slug($role, '-');

			if (isset($this->acl[$role.'/'.$action])) return $this->acl[$role.'/'.$action];
		}

		return false;
	}

	/**
	 * Assign single or multiple $roles + $actions to have $type access
	 * 
	 * @access  public
	 * @param   mixed   $roles          A string or an array of roles
	 * @param   mixed   $actions        A string or an array of action name
	 * @param   bool    $type
	 * @return  bool
	 * @throws  AclException
	 */
	public function allow($roles, $actions, $allow = true) 
	{
		if ( ! is_array($roles)) 
		{
			switch (true)
			{
				case $roles === '*' :
					$roles = $this->roles;
					break;
				case $roles[0] === '!' :
					$roles = array_diff($this->roles, array(substr($roles, 1)));
					break;
				default :
					$roles = array($roles);
					break;
			}
			
		}

		if ( ! is_array($actions)) 
		{
			switch (true)
			{
				case $actions === '*' :
					$actions = $this->actions;
					break;
				case $actions[0] === '!' :
					$actions = array_diff($this->actions, array(substr($actions, 1)));
					break;
				default :
					$actions = array($actions);
					break;
			}
		}

		foreach ($roles as $role) 
		{
			$role = Str::slug($role, '-');

			if ( ! $this->has_role($role)) 
			{
				throw new AclException(__METHOD__.": Role {$role} does not exist.");
			}

			foreach ($actions as $action) 
			{
				$action = Str::slug($action, '-');

				if ( ! $this->has_action($action)) 
				{
					throw new AclException(__METHOD__.": Action {$action} does not exist.");
				}

				$id             = $role.'/'.$action;
				$this->acl[$id] = $allow;
			}
		}

		return true;
	}

	/**
	 * Shorthand function to deny access for single or multiple $roles and $resouces
	 * 
	 * @access  public
	 * @param   mixed   $roles          A string or an array of roles
	 * @param   mixed   $actions        A string or an array of action name
	 * @return  bool
	 */
	public function deny($roles, $actions) 
	{
		return $this->allow($roles, $actions, false);
	}

}