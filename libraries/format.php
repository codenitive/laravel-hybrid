<?php namespace Hybrid;

/**
 * Format class
 *
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @package    Fuel
 * @category   Core
 * @author     Fuel Development Team
 * @copyright  2010 - 2012 Fuel Development Team
 * @link       http://docs.fuelphp.com/classes/format.html
 */

use \Input, \Str;

class Format {

	/**
	 * @var  array|mixed  input to convert
	 */
	protected $data = array();

	/**
	 * Returns an instance of the Format object.
	 *
	 *     echo Format::make(array('foo' => 'bar'))->to_xml();
	 *
	 * @access  public
	 * @param   mixed       general date to be converted
	 * @param   string      data format the file was provided in
	 * @return  Format
	 * @throws  Exception
	 */
	public static function make($data = null, $from_type = null)
	{
		return new static($data, $from_type);
	}

	/**
	 * Return an instance of Format
	 *
	 * @access  protected
	 * @param   mixed       general date to be converted
	 * @param   string      data format the file was provided in
	 * @return  void
	 * @throws  Exception
	 */
	protected function __construct($data = null, $from_type = null)
	{
		// If the provided data is already formatted we should probably 
		// convert it to an array
		if ($from_type !== null)
		{
			if ( ! method_exists($this, 'from_' . $from_type))
			{
				throw new Exception('Format class does not support conversion from "' . $from_type . '".');
			}

			$data = call_user_func(array($this, 'from_' . $from_type), $data);
		}

		$this->data = $data;
	}

	/**
	 * To array conversion
	 *
	 * Goes through the input and makes sure everything is either a scalar 
	 * value or array
	 *
	 * @access  public
	 * @param   mixed       $data
	 * @return  array
	 */
	public function to_array($data = null)
	{
		if ($data === null) $data = $this->data;

		$array = array();

		if (is_object($data) and ! $data instanceof \Iterator)
		{
			$data = get_object_vars($data);
		}

		if (empty($data)) return array();

		foreach ($data as $key => $value)
		{
			$array[$key] = (is_object($value) or is_array($value)) ? $this->to_array($value) : $value;
		}

		return $array;
	}

	/**
	 * To XML conversion
	 *
	 * @param   mixed        $data
	 * @param   null         $structure
	 * @param   null|string  $basenode
	 * @return  string
	 */
	public function to_xml($data = null, $structure = null, $basenode = 'xml')
	{
		if ($data == null) $data = $this->data;

		// turn off compatibility mode as simple xml throws a wobbly if you 
		// don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		{
			ini_set('zend.ze1_compatibility_mode', 0);
		}

		if ($structure == null)
		{
			$structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
		}

		// Force it to be something useful
		if ( ! is_array($data) and ! is_object($data)) $data = (array) $data;

		foreach ($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				$key = (Str::singular($basenode) != $basenode) ? Str::singular($basenode) : 'item';
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z_\-0-9]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value) or is_object($value))
			{
				$node = $structure->addChild($key);

				// recursive call if value is not empty
				if( ! empty($value))
				{
					$this->to_xml($value, $node, $key);
				}
			}

			else
			{
				// add single node.
				$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");

				$structure->addChild($key, $value);
			}
		}

		// pass back as string. or simple xml object if you want!
		return $structure->asXML();
	}

	/**
	 * To CSV conversion
	 *
	 * @param   mixed   $data
	 * @param   mixed   $separator
	 * @return  string
	 */
	public function to_csv($data = null, $separator = ',')
	{
		if ($data === null) $data = $this->data;

		if (is_object($data) and ! $data instanceof \Iterator)
		{
			$data = $this->to_array($data);
		}

		// Multi-dimensional array
		if (is_array($data) and $this->is_multi($data))
		{
			$data = array_values($data);

			if ($this->is_assoc($data[0]))
			{
				$headings = array_keys($data[0]);
			}
			else
			{
				$headings = array_shift($data);
			}
		}
		// Single array
		else
		{
			$headings = array_keys((array) $data);
			$data     = array($data);
		}

		$output = "\"".implode('"' . $separator . '"', $headings) . "\"\n";

		foreach ($data as &$row)
		{
			$output .= '"' . implode('"' . $separator . '"', (array) $row) . "\"\n";
		}

		return rtrim($output, "\n");
	}

	/**
	 * To JSON conversion
	 *
	 * @param   mixed  $data
	 * @param   bool   wether to make the json pretty
	 * @return  string
	 */
	public function to_json($data = null, $pretty = false)
	{
		if ($data == null) $data = $this->data;

		// To allow exporting ArrayAccess objects like Orm\Model instances 
		// they need to be converted to an array first
		$data = (is_array($data) or is_object($data)) ? $this->to_array($data) : $data;
		return $pretty ? static::pretty_json($data) : json_encode($data);
	}

	/**
	 * To JSONP conversion
	 *
	 * @param   mixed  $data
	 * @param   bool   wether to make the json pretty
	 * @return  string
	 */
	public function to_jsonp($data = null, $pretty = false)
	{
		if ($data == null) $data = $this->data;

		$callback = Input::get('callback');
		is_null($callback) and $callback = 'response';

		return $callback.'('.$this->to_json($data, $pretty).')';
	}

	/**
	 * Serialize
	 *
	 * @param   mixed  $data
	 * @return  string
	 */
	public function to_serialized($data = null)
	{
		if ($data == null) $data = $this->data;

		return serialize($data);
	}

	/**
	 * Return as a string representing the PHP structure
	 *
	 * @param   mixed  $data
	 * @return  string
	 */
	public function to_php($data = null)
	{
		if ($data == null) $data = $this->data;

		return var_export($data, true);
	}

	/**
	 * Import XML data
	 *
	 * @param   string  $string
	 * @return  array
	 */
	protected function from_xml($string)
	{
		$string = is_string($string) ? simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : $string;
		$data   = array();

		// Convert all objects SimpleXMLElement to array recursively
		foreach ( (array) $string as $key => $value)
		{
			$data[$key] = (is_array($value) or is_object($value)) ? $this->from_xml($value) : $value;
		}

		return $data;
	}

	/**
	 * Import CSV data
	 *
	 * @param   string  $string
	 * @return  array
	 */
	protected function from_csv($string)
	{
		$data = array();

		// Splits
		$rows = explode("\n", trim($string));

		$map = function($value) 
		{
			return trim($value, '"');
		};

		// TODO: This means any headers with , will be split, but this is less 
		// likely thay a value containing it
		$headings = array_map($map, explode(',', array_shift($rows)));

		$join_row = null;

		foreach ($rows as $row)
		{
			// Check for odd numer of double quotes
			while (substr_count($row, '"') % 2)
			{
				// They have a line start to join onto
				if ($join_row !== null)
				{
					// Lets stick this row onto a new line after the existing 
					// row, and see what happens
					$row = $join_row."\n".$row;

					// Did that fix it?
					if (substr_count($row, '"') % 2)
					{
						// Nope, lets try adding the next line
						continue 2;
					}

					else
					{
						// Yep, lets kill the join row.
						$join_row = null;
					}
				}

				// Lets start a new "join line"
				else
				{
					$join_row = $row;

					// Lets bust outta this join, and go to the 
					// next row (foreach)
					continue 2;
				}
			}

			// If present, remove the " from start and end
			substr($row, 0, 1) === '"' and $row = substr($row, 1);
			substr($row, -1) === '"' and $row = substr($row, 0, -1);

			// Extract the fields from the row
			$data_fields = explode('","', $row);

			if (count($data_fields) == count($headings))
			{
				$data[] = array_combine($headings, $data_fields);
			}

		}

		return $data;
	}

	/**
	 * Import JSON data
	 *
	 * @param   string  $string
	 * @return  mixed
	 */
	private function from_json($string)
	{
		return json_decode(trim($string));
	}

	/**
	 * Import Serialized data
	 *
	 * @param   string  $string
	 * @return  mixed
	 */
	private function from_serialize($string)
	{
		return unserialize(trim($string));
	}

	/**
	 * Makes json pretty the json output.
	 * Barrowed from http://www.php.net/manual/en/function.json-encode.php#80339
	 *
	 * @param   string  $json  json encoded array
	 * @return  string|false  pretty json output or false when the input was not valid
	 */
	protected static function pretty_json($data)
	{
		$json = json_encode($data);

		if ( ! $json) return false;

		$tab          = "\t";
		$newline      = "\n";
		$new_json     = "";
		$indent_level = 0;
		$in_string    = false;
		$len          = strlen($json);

		for ($c = 0; $c < $len; $c++)
		{
			$char = $json[$c];

			switch($char)
			{
				case '{':
				case '[':
					if ( ! $in_string)
					{
						$new_json .= $char.$newline.str_repeat($tab, $indent_level+1);
						$indent_level++;
					}
					else
					{
						$new_json .= $char;
					}
					break;
				case '}':
				case ']':
					if ( ! $in_string)
					{
						$indent_level--;
						$new_json .= $newline.str_repeat($tab, $indent_level).$char;
					}
					else
					{
						$new_json .= $char;
					}
					break;
				case ',':
					if ( ! $in_string)
					{
						$new_json .= ','.$newline.str_repeat($tab, $indent_level);
					}
					else
					{
						$new_json .= $char;
					}
					break;
				case ':':
					if ( ! $in_string)
					{
						$new_json .= ': ';
					}
					else
					{
						$new_json .= $char;
					}
					break;
				case '"':
					if ($c > 0 and $json[$c-1] !== '\\')
					{
						$in_string = ! $in_string;
					}
				default:
					$new_json .= $char;
					break;
			}
		}

		return $new_json;
	}

	/**
	 * Check if given array is multi dimentional array
	 *
	 * @access protected
	 * @param  array   $arr      
	 * @param  boolean $all_keys 
	 * @return boolean           
	 */
	protected function is_multi($arr, $all_keys = false)
	{
		$values = array_filter($arr, 'is_array');
		return $all_keys ? count($arr) === count($values) : count($values) > 0;
	}

	/**
	 * Check if given array is an associated array
	 *
	 * @access protected
	 * @param  array   $arr 
	 * @return boolean      
	 */
	protected function is_assoc($arr)
	{
		if ( ! is_array($arr)) return false;

		return ! is_int(key($arr));
	}
}