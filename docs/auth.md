# Hybrid Auth

## The Contents

- [Configuration](#configuration)
- [Convention](#convention)

<a name="configuration"></a>
## Configuration

`Hybrid\Auth` extends the functionality of `Laravel\Auth` with the extra functionality to retrieve users' role. This is important when we want to use `Hybrid\Acl` to manage application Access Control List (ACL).

Laravel Hybrid would need to know the list of roles (name) associated to the current user, in relevant to all role defined in `\Hybrid\Acl::add_role()`.

    'roles' => function ($id, $roles)
	{
	 	 \User_Role::with('role')->where('user_id', '=', $user_id)->get();
	 
	 	 foreach ($user_roles as $role)
	 	 {
	 	 	 array_push($roles, $role->role->name);
	 	 }
	 	 
	 	 return $roles;
	 }
	    
<a name="convention"></a>
## Convention

Alternatively, we can ignore **[Configuration](#configuration)** by using Eloquent Orm driver with this associations.

### User Model

	<?php
	
	class User extends Eloquent 
	{
		public function roles()
		{
			return $this->has_many('User_Role');
		}
	
	}

### Role Model

	<?php

	class Role extends Eloquent 
	{
		public function users()
		{
			return $this->has_many('User_Role');
		}
	}

### User_Role Model 

	<?php

	class User_Role extends Eloquent
	{
		public function role()
		{
			return $this->belongs_to('Role');
		}

		public function user()
		{
			return $this->belongs_to('User');
		}
	}

