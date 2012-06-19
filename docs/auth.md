# Hybrid Auth

## The Contents

- [Introduction](#introduction)
- [Configuration](#configuration)
- [Event](#event)
- [Convention](#convention)

<a name="introduction"></a>
## Introduction

`Hybrid\Auth` extends the functionality of `Laravel\Auth` with the extra functionality to retrieve users' role. This is important when we want to use `Hybrid\Acl` to manage application Access Control List (ACL).

<a name="configuration"></a>
## Configuration

Laravel Hybrid would need to know the list of roles (name) associated to the current user, in relevant to all role defined in `\Hybrid\Acl::add_role()`. This can be done by editing `hybrid/config/auth.php`.

    'roles' => function ($user_id, $roles)
	{
		if ( ! class_exists('Role_User', true)) return null;

	 	$user_roles = \Role_User::with('roles')->where('user_id', '=', $user_id)->get();
	 
	 	foreach ($user_roles as $role)
	 	{
	 	 	array_push($roles, $role->roles->name);
	 	}
	 	 
	 	return $roles;
	 }

<a name="event"></a>
## Event

By using Event, the default configuration can be overwritten easily without modifying the config file as mention above.

	Event::listen('hybrid.auth.roles', function ($user_id, $roles)
	{
		return array('manager', 'admin');
	});

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

	class Role_User extends Eloquent
	{
		public function roles()
		{
			return $this->belongs_to('Role');
		}

		public function users()
		{
			return $this->belongs_to('User');
		}
	}

