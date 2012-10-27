Hybrid Bundle for Laravel
==============

A set of class that extends the functionality of Laravel without affecting the standard workflow when the application doesn't actually utilize Hybrid feature.

[![Build Status](https://secure.travis-ci.org/codenitive/laravel-hybrid.png)](http://travis-ci.org/codenitive/laravel-hybrid)

## Installation

Installation with Laravel Artisan

	php artisan bundle:install hybrid

### Bundle Registration

	'hybrid' => array('auto' => true),

## Key Features

* ACL class support unlimited roles and actions.
* Auth class support roles (for ACL class integration).
* Chart collection class using [Google Visualization Library](http://code.google.com/apis/chart/).

## Hybrid Documentation

Hybrid Bundle come with an offline documentation, to view this please download and enable `bundocs` bundle, 
see [Bundocs Bundle](http://bundles.laravel.com/bundle/bundocs) for more detail.

## Contributors

* [Mior Muhammad Zaki](http://git.io/crynobone) 

## License

	The MIT License

	Copyright (C) 2012 by Mior Muhammad Zaki <http://git.io/crynobone> 

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
