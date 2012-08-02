# Hybrid Format

The Format class (forked from [FuelPHP](http://fuelphp.com)) helps you convert between various formats such as XML, JSON, CSV, etc.

	@author     Fuel Development Team
	@copyright  2010 - 2012 Fuel Development Team
	@link       http://docs.fuelphp.com/classes/format.html

## Methods

### make($data = null, $from_type = null)

Returns an instance of the `Hybrid\Format` object.

	@static
	@param   string  $name      instance name
	@param   array   $config
	@return  Hybrid\Format
	@throws  Hybrid\Exception
	
	$array = array('foo' => 'bar');
	print_r(Hybrid\Format::make($array));
	
	// Returns
	Hybrid\Format Object
	(
    	[data:protected] => Array
    	(
    	    [foo] => bar
    	)
	)
	
### to_array($data = null)

The `to_array()` method returns the given data as an array. Do not call this directly, use the `make()` method as described in the example.

	@param   mixed   $data      data to be converted. 
	@return  array
	
	$json_string = '{"foo":"bar","baz":"qux"}';
	print_r(Hybrid\Format::make($json_string, 'json')->to_array());

	// Returns
	Array
	(
	    [foo] => bar
	    [baz] => qux
	)


### to_csv($data = null, $separator = ',')

The `to_csv()` method returns the given data as a CSV string. Do not call this directly, use the `make()` method as described in the example.

	@param   mixed   $data      data to be converted.
	@param   mixed   $separator
	@return  string
	
	$json_string = '{"foo":"bar","baz":"qux"}';
	print_r(Hybrid\Format::make($json_string, 'json')->to_csv());

	// Returns
	// foo,baz
	// "bar","qux"

### to_json($data = null, $pretty = false)

The `to_json()` method returns the given data as a JSON string. Do not call this directly, use the `make()` method as described in the example.

	@param   mixed   $data      data to be converted.
	@param   bool    $pretty    wether to make the json pretty
	@return  string
	
	$array = array('foo' => 'bar', 'baz' => 'qux');
	print_r(Hybrid\Format::make($array)->to_json());

	// Returns
	// {"foo":"bar","baz":"qux"}
	
### to_jsonp($data = null, $pretty = false)

The `to_jsonp()` method returns the given data as a JSON string with callback. Do not call this directly, use the `make()` method as described in the example.

	@param   mixed   $data      data to be converted.
	@param   bool    $pretty    wether to make the json pretty
	@return  string
	
	$array = array('foo' => 'bar', 'baz' => 'qux');
	print_r(Input::get('callback'));
	print_r(Hybrid\Format::make($array)->to_jsonp());

	// Returns
	// foobar
	// foobar({"foo":"bar","baz":"qux"})

### to_jsonp($data = null, $pretty = false)

The `to_jsonp()` method returns the given data as a JSON string with callback. Do not call this directly, use the `make()` method as described in the example.

	@param   mixed   $data      data to be converted.
	@param   bool    $pretty    wether to make the json pretty
	@return  string
	
	$array = array('foo' => 'bar', 'baz' => 'qux');
	print_r(Input::get('callback'));
	print_r(Hybrid\Format::make($array)->to_jsonp());

	// Returns
	// foobar
	// foobar({"foo":"bar","baz":"qux"})

### to_php($data = null)

The `to_php()` method returns the given data as a PHP representation of the data in a string. You could pass this into `eval()` or use it for other crazy things.

	@param   mixed   $data      data to be converted.
	@return  string
	
	$array = array(1, 2, array('a', 'b', 'c'));
	print_r(Hybrid\Format::make($array)->to_php());

	// Returns
	Array
	(
	    0 => 1,
	    1 => 2,
	    2 => array(
	        0 => 'a',
	        1 => 'b',
	        2 => 'c',
	    ),
	)

### to_serialized($data = null)

The `to_serialized()` method returns the given data as a serialized string. Do not call this directly, use the `make()` method as described in the example.

	@param   mixed        $data      data to be converted.
	@return  string
	
	$array = array('foo' => 'bar', 'baz' => 'qux');
	print_r(Hybrid\Format::make($array)->to_serialized());

	// Returns
	// a:2:{s:3:"foo";s:3:"bar";s:3:"baz";s:3:"qux";}