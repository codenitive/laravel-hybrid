# Hybrid Response

Response class extends `Laravel\Response` class to allow RESTful response using either normal `View`, JSON, XML, CSV, serialize or etc. It will check for `Input::get('format')` given in the request to properly define the data for output.

## Example

	Route::get('api', function()
	{
		$data = array(
			'foo' => 'bar',
		);
		
		$view = View::make('home.index', $data);
		
		return Hybrid\Response::restful(array(
			'view' => $view,
			'data' => $data,
		), 200);
	});
	
	// In application/views/home/index.php
	<h1>Hello <?php echo $foo; ?></h1>
	
Given the example, when user access as:

### /api 
	
	<h1>Hello bar</h1>

### /api?format=json
	
	{"foo":"bar"}

### /api?format=xml
	 
	<?xml version="1.0" encoding="utf-8"?>
	<xml>
		<foo>bar</foo>
	</xml>
	
In event where view is not defined and [Format](/bundocs/hybrid/classes/format) is not available, `Response::error('500')` will be thrown.