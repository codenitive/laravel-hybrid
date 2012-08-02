# Hybrid Auth

## The Contents

- [Introduction](#introduction)
- [Configuration](#configuration)
- [Event](#event)

<a name="introduction"></a>
## Introduction

`Hybrid\Auth` extends the functionality of `Laravel\Auth` with the extra functionality to retrieve users' role. This is important when we want to use `Hybrid\Acl` to manage application Access Control List (ACL).

<a name="configuration"></a>
## Configuration

Laravel Hybrid would need to know the list of roles (name) associated to the current user, in relevant to all role defined in `\Hybrid\Acl::add_role()`. This can be done by editing `hybrid/config/auth.php`.

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
	 }

To archieve this using Eloquent Orm driver, the following structure need to be defined.

### User Model

	<?php
	
	class User extends Eloquent 
	{
		public function roles()
		{
			return $this->has_many_and_belongs_to('Role', 'role_user');
		}
	
	}

### Role Model

	<?php

	class Role extends Eloquent 
	{
		public function users()
		{
			return $this->has_many_and_belongs_to('User', 'role_user');
		}
	}

### Role_User Model (optional)

	<?php

	class Role_User extends Eloquent
	{
		public static $table = 'role_user';

		public function roles()
		{
			return $this->belongs_to('Role');
		}

		public function users()
		{
			return $this->belongs_to('User');
		}
	}


<a name="event"></a>
## Event

By using Event, the default configuration can be overwritten easily without modifying the config file as mention above.

	Event::listen('hybrid.auth.roles', function ($user, $roles)
	{
		return array('manager', 'admin');
	});
