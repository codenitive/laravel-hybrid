# Hybrid Curl

## Contents

* [Introduction](#introduction)
* [Methods](#methods)

<a name="introduction"></a>
## Introduction

`Hybrid\Curl` class processes external URI requests.

### Example

	$google = Hybrid\Curl::make("GET http://google.com");
	
	$google->option(array(
		CURLOPT_RETURNTRANSFER => 1,
	));

	$response = $google->call();
	
<a name="methods"></a>
## Methods

### make($uri, $data = array())

Create a new Curl instance.

	@static
	@param  String 	$uri 		The URI being requested (including HTTP method). 
								If this parameter is not present, the URI class 
								will be used to detect the URI.
	@param  Array	$data 		An array of GET, POST, PUT or DELETE dataset (depends on which HTTP method is called)
	@return self
	
	// GET method support query string
	$curl = Hybrid\Curl::make('GET http://google.com?q=hello world');

	// Alternatively
	$curl = Hybrid\Curl::make('GET http://google.com', array('q' => 'hello world'));

#### Aliases

##### get($uri, $data)

Initiate this class as a new object using GET.

	$curl = Hybrid\Curl::get('http://google.com', array('q' => 'hello world'));

##### post($uri, $data)

Initiate this class as a new object using POST.

	$curl = Hybrid\Curl::post('http://google.com', array('q' => 'hello world'));

##### put($uri, $data)

Initiate this class as a new object using PUT.

	$curl = Hybrid\Curl::put('http://google.com', array('q' => 'hello world'));
	
##### delete($uri, $data)

Initiate this class as a new object using DELETE.

	$curl = Hybrid\Curl::delete('http://google.com', array('q' => 'hello world'));
	
### option($name, $value = null)

Set curl option(s).

	@param  mixed 	$name		Either an array (key as CURL option) or a string of 
								CURL option name
	@param  mixed 	$value		CURL option value
	@return self
	
	$curl->option(CURLOPT_HEADER, true);

	// or use array to assign multiple value
	$curl->option(array(
		CURLOPT_HEADER => true,
		CURLOPT_NOBODY => false,
	));
	
### call()

Execute the Curl request and return the output.

	@return Object
	
	$curl->call();