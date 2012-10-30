<?php namespace Hybrid;

/**
 * Acl class
 *
 * @package    Hybrid
 * @category   Acl
 * @author     Laravel Hybrid Development Team
 */

use \Str;

class Acl
{
	/**
	 * Acl initiated status
	 *
	 * @static
	 * @access  protected
	 * @var     boolean
	 */
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
	 * @param   string        $name
	 * @param   Memory\Driver $memory
	 * @return  self
	 */
	public static function make($name = null, Memory\Driver $memory = null)
	{
		if (is_null($name)) $name = 'default';

		if ( ! isset(static::$instances[$name]))
		{
			static::$instances[$name] = new static($name, $memory);
		}

		return static::$instances[$name];
	}

	/**
	 * Register an Acl instance with Closure.
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name
	 * @param   Closure $callback
	 * @return  self
	 */
	public static function register($name, $callback = null)
	{
		if (is_callable($name))
		{
			$callback = $name;
			$name     = null;
		}

		$instance = static::make($name);

		$callback($instance);

		return $instance;
	}

	/**
	 * Construct a new object.
	 *
	 * @access  protected
	 * @param   string        $name
	 * @param   Memory\Driver $memory
	 */
	protected function __construct($name, Memory\Driver $memory = null) 
	{
		$this->name = $name;

		$this->add_role('guest');

		$this->attach($memory);
	}

	protected $name = null;

	protected $memory = null;

	/**
	 * List of roles
	 * 
	 * @access  protected
	 * @var     array
	 */
	protected $roles = array();
	 
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
	 * Bind current Acl instance with a Registry
	 *
	 * @access  public				
	 * @param   Memory\Driver   $memory
	 * @return  self
	 * @throws  Exception
	 */
	public function attach(Memory\Driver $memory = null)
	{
		if ( ! is_null($this->memory))
		{
			throw new Exception(__METHOD__.": Unable to assign multiple Hybrid\Memory instance.");
		}

		// since we already check instanceof, only check for NULL
		if (is_null($memory))
		{
			return;
		}

		$this->memory = $memory;

		$data = $this->memory->get("acl_".$this->name, array());

		$data = array_merge(array(
			'acl'     => array(),
			'actions' => array(),
			'roles'   => array(),
		), $data);

		// Loop through all the roles in memory and add it to
		// this ACL instance.
		foreach ($data['roles'] as $role)
		{
			if ( ! $this->has_role($role)) $this->add_role($role);
		}

		// Loop through all the actions in memory and add it to 
		// this ACL instance.
		foreach ($data['actions'] as $action)
		{
			if ( ! $this->has_action($action)) $this->add_action($action);
		}

		// Loop through all the acl in memory and add it to 
		// this ACL instance.
		foreach ($data['acl'] as $id => $allow)
		{
			if (strpos($id, '/') !== false)
			{
				list($role, $action) = explode('/', $id);
				$this->allow($role, $action, $allow);
			}

			if (strpos($id, ':') !== false)
			{
				list($role, $action) = explode(':', $id);
				$this->acl($role, $action, $allow);
			}
		}

		/*
		 * Re-sync memory with acl instance, make sure anything
		 * that added before ->with($memory) got called is appended
		 * to memory as well.
		 */
		$this->memory->put("acl_".$this->name.".actions", $this->actions);
		$this->memory->put("acl_".$this->name.".roles", $this->roles);
		$this->memory->put("acl_".$this->name.".acl", $this->acl);

		return $this;
	}

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
		$role = trim(Str::slug($role, '-'));

		if ( ! empty($role) and in_array($role, $this->roles)) return true;

		return false;
	}

	/**
	 * Add multiple user' roles to the this instance
	 * 
	 * @access  public
	 * @param   mixed   $roles      A string or an array of roles
	 * @return  self
	 * @throws  AclException
	 */
	public function add_roles($roles = null)
	{
		
		foreach ((array) $roles as $role)
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

		return $this;
	}

	/**
	 * Add single user' role to the this instance
	 * 
	 * @access  public
	 * @param   mixed   $role       A string or an array of roles
	 * @return  self
	 * @throws  AclException
	 */
	public function add_role($role)
	{
		if (is_null($role)) 
		{
			throw new AclException(__FUNCTION__.": Can't add NULL role.");
		}

		$role = trim(Str::slug($role, '-'));

		if ($this->has_role($role))
		{
			throw new AclException(__FUNCTION__.": Role {$role} already exist.");
		}

		array_push($this->roles, $role);

		if (! empty($this->memory)) $this->memory->put("acl_".$this->name.".roles", $this->roles);

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
		$action = trim(Str::slug($action, '-'));

		if ( ! empty($action) and in_array($action, $this->actions)) return true;

		return false;
	}

	/**
	 * Add multiple actions to this instance
	 * 
	 * @access  public
	 * @param   mixed   $actions    A string of action name
	 * @return  self
	 * @throws  AclException
	 */
	public function add_actions($actions = null) 
	{
		foreach ((array) $actions as $action)
		{
			try
			{
				$this->add_action($action);
			}
			catch (AclException $e)
			{
				continue;
			}
		}

		return $this;
	}

	/**
	 * Add single action to this instance
	 * 
	 * @access  public
	 * @param   mixed   $action     A string of action name
	 * @return  self
	 * @throws  AclException
	 */
	public function add_action($action) 
	{
		if (is_null($action)) 
		{
			throw new AclException(__FUNCTION__.": Can't add NULL actions.");
		}

		$action = trim(Str::slug($action, '-'));
		
		if ($this->has_action($action))
		{
			throw new AclException(__FUNCTION__.": Action {$action} already exist.");
		}	

		array_push($this->actions, $action);

		if (! empty($this->memory)) $this->memory->put("acl_".$this->name.".actions", $this->actions);

		return $this;
	}

	/**
	 * Verify whether current user has sufficient roles to access the actions based 
	 * on available type of access.
	 *
	 * @access  public
	 * @param   mixed   $action     A string of action name
	 * @return  bool
	 * @throws  AclException
	 */
	public function can($action) 
	{
		$roles = array();

		if ( ! in_array(Str::slug($action, '-'), $this->actions)) 
		{
			throw new AclException(__FUNCTION__.": Unable to verify unknown action {$action}.");
		}

		if (is_null(Auth::user()))
		{
			// only add guest if it's available
			if (in_array('guest', $this->roles)) array_push($roles, 'guest');
		}
		else $roles = Auth::roles();

		$action     = Str::slug($action, '-');
		$action_key = array_search($action, $this->actions);

		// array_search() will return false when no key is found based on given haystack,
		// therefore we should just ignore and return false
		if ($action_key === false) return false;

		foreach ((array) $roles as $role) 
		{
			$role     = Str::slug($role, '-');
			$role_key = array_search($role, $this->roles);

			// array_search() will return false when no key is found based on given haystack,
			// therefore we should just ignore and continue to the next role.
			if ($role_key === false) continue;

			if (isset($this->acl[$role_key.':'.$action_key])) return $this->acl[$role_key.':'.$action_key];
		}

		return false;
	}

	/**
	 * Assign single or multiple $roles + $actions to have access
	 * 
	 * @access  public
	 * @param   mixed   $roles          A string or an array of roles
	 * @param   mixed   $actions        A string or an array of action name
	 * @param   bool    $allow
	 * @return  self
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
				throw new AclException(__FUNCTION__.": Role {$role} does not exist.");
			}

			foreach ($actions as $action) 
			{
				$action = Str::slug($action, '-');

				if ( ! $this->has_action($action)) 
				{
					throw new AclException(__FUNCTION__.": Action {$action} does not exist.");
				}

				$this->acl($role, $action, $allow);
			}
		}

		return $this;
	}

	/**
	 * Assign a key combination of $roles + $actions to have access
	 * 
	 * @access  protected
	 * @param   mixed   $roles          A key or string representation of roles
	 * @param   mixed   $actions        A key or string representation of action name
	 * @param   bool    $allow
	 * @return  void
	 */
	protected function acl($role, $action, $allow = true)
	{
		$role_key   = is_numeric($role) ? $role : array_search($role, $this->roles);
		$action_key = is_numeric($action) ? $action : array_search($action, $this->actions);

		$id             = $role_key.':'.$action_key;
		$this->acl[$id] = $allow;

		if ( ! empty($this->memory))
		{
			$value = array_merge(
				$this->memory->get("acl_".$this->name.".acl", array()), 
				array("{$role_key}:{$action_key}" => $allow)
			);
			
			$this->memory->put("acl_".$this->name.".acl", $value);
		}
	}

	/**
	 * Shorthand function to deny access for single or multiple 
	 * $roles and $actions
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

	/**
	 * Shutdown Hybrid\Acl
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function shutdown()
	{
		static::$initiated = false;
		static::$instances = array();
	}

}