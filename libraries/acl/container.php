<?php namespace Hybrid\Acl;

/**
 * Acl class
 *
 * @package    Hybrid
 * @category   Acl
 * @author     Laravel Hybrid Development Team
 */

use \Str,
	Hybrid\AclException,
	Hybrid\InvalidArgumentException,
	Hybrid\RuntimeException,
	Hybrid\Auth as Auth,
	Hybrid\Memory\Driver as MemoryDriver;

class Container {
	
	/**
	 * Acl instance name.
	 * 
	 * @access  protected
	 * @var     string
	 */
	protected $name = null;

	/**
	 * Memory instance.
	 * 
	 * @access  protected
	 * @var     Hybrid\Memory\Driver
	 */
	protected $memory = null;

	/**
	 * List of roles
	 * 
	 * @access  protected
	 * @var     Hybrid\Acl\Fluent
	 */
	protected $roles = null;
	 
	/**
	 * List of actions
	 * 
	 * @access  protected
	 * @var     Hybrid\Acl\Fluent
	 */
	protected $actions = null;
	 
	/**
	 * List of ACL map between roles, action
	 * 
	 * @access  protected
	 * @var     array
	 */
	protected $acl = array();

	/**
	 * Construct a new object.
	 *
	 * @access  public
	 * @param   string        $name
	 * @param   MemoryDriver  $memory
	 */
	public function __construct($name, MemoryDriver $memory = null) 
	{
		$this->name    = $name;
		$this->roles   = new Fluent('roles');
		$this->actions = new Fluent('actions');

		$this->roles->add('guest');
		$this->attach($memory);
	}

	/**
	 * Bind current Acl instance with a Registry
	 *
	 * @access  public				
	 * @param   MemoryDriver    $memory
	 * @return  self
	 * @throws  Exception
	 */
	public function attach(MemoryDriver $memory = null)
	{
		if ( ! is_null($this->memory))
		{
			throw new RuntimeException(
				"Unable to assign multiple Hybrid\Memory instance."
			);
		}

		// since we already check instanceof, only check for NULL
		if (is_null($memory)) return;

		$this->memory = $memory;
		$data         = array_merge(array(
			'acl'     => array(),
			'actions' => array(),
			'roles'   => array(),
		), $this->memory->get("acl_".$this->name, array()));

		// Loop through all the roles in memory and add it to
		// this ACL instance.
		foreach ($data['roles'] as $role)
		{
			$this->roles->add($role);
		}

		// Loop through all the actions in memory and add it to 
		// this ACL instance.
		foreach ($data['actions'] as $action)
		{
			$this->actions->add($action);
		}

		// Loop through all the acl in memory and add it to 
		// this ACL instance.
		foreach ($data['acl'] as $id => $allow)
		{
			list($role, $action) = explode(':', $id);
			$this->assign($role, $action, $allow);
		}

		$this->sync();
	}

	/**
	 * Sync memory with acl instance, make sure anything that added before 
	 * ->with($memory) got called is appended to memory as well.
	 *
	 * @access public
	 * @return void
	 */
	public function sync()
	{
		if ( ! is_null($this->memory))
		{
			$this->memory->put("acl_".$this->name.".actions", $this->actions->get());
			$this->memory->put("acl_".$this->name.".roles", $this->roles->get());
			$this->memory->put("acl_".$this->name.".acl", $this->acl);
		}

		return $this;
	}

	/**
	 * Verify whether current user has sufficient roles to access the 
	 * actions based on available type of access.
	 *
	 * @access  public
	 * @param   mixed   $action     A string of action name
	 * @return  bool
	 * @throws  Exception
	 */
	public function can($action) 
	{
		$roles   = array();
		$actions = $this->actions->get();

		if ( ! in_array(Str::slug($action, '-'), $actions)) 
		{
			throw new InvalidArgumentException(
				"Unable to verify unknown action {$action}."
			);
		}

		if (is_null(Auth::user()))
		{
			// only add guest if it's available
			if ($this->roles->has('guest')) array_push($roles, 'guest');
		}
		else $roles = Auth::roles();

		$action     = Str::slug($action, '-');
		$action_key = array_search($action, $actions);

		// array_search() will return false when no key is found based on 
		// given haystack, therefore we should just ignore and return false
		if ($action_key === false) return false;

		foreach ((array) $roles as $role) 
		{
			$role     = Str::slug($role, '-');
			$role_key = array_search($role, $this->roles->get());

			// array_search() will return false when no key is found based 
			// on given haystack, therefore we should just ignore and 
			// continue to the next role.
			if ($role_key === false) continue;

			if (isset($this->acl[$role_key.':'.$action_key]))
			{
				return $this->acl[$role_key.':'.$action_key];
			}
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
	 * @throws  Exception
	 */
	public function allow($roles, $actions, $allow = true) 
	{
		if ( ! is_array($roles)) 
		{
			switch (true)
			{
				case $roles === '*' :
					$roles = $this->roles->get();
					break;
				case $roles[0] === '!' :
					$roles = array_diff($this->roles->get(), array(substr($roles, 1)));
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
					$actions = $this->actions->get();
					break;
				case $actions[0] === '!' :
					$actions = array_diff($this->actions->get(), array(substr($actions, 1)));
					break;
				default :
					$actions = array($actions);
					break;
			}
		}

		foreach ($roles as $role) 
		{
			$role = Str::slug($role, '-');

			if ( ! $this->roles->has($role)) 
			{
				throw new AclException("Role {$role} does not exist.");
			}

			foreach ($actions as $action) 
			{
				$action = Str::slug($action, '-');

				if ( ! $this->actions->has($action)) 
				{
					throw new AclException("Action {$action} does not exist.");
				}

				$this->assign($role, $action, $allow);
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
	protected function assign($role, $action, $allow = true)
	{
		$role_key   = is_numeric($role) ? $role : array_search($role, $this->roles->get());
		$action_key = is_numeric($action) ? $action : array_search($action, $this->actions->get());
		$key        = $role_key.':'.$action_key;

		$this->acl[$key] = $allow;
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
	 * Magic method to mimic roles and actions manipulation
	 */
	public function __call($method, $parameters)
	{
		$operation = null;
		$type      = null;

		if (preg_match('/^(add|has|remove)_(role|action)(s?)$/', $method, $matches))
		{
			$operation = $matches[1];
			$type      = $matches[2].'s';

			if (isset($matches[3]) and $matches[3] === 's' and $operation === 'add')
			{
				$operation = 'fill';
			}

			$result = call_user_func_array(array($this->{$type}, $operation), $parameters);

			if ($operation === 'has') return $result;
		}

		return $this;
	}
}