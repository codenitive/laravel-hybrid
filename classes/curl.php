<?php namespace Hybrid;

use \Exception, \stdClass;

class Curl
{
	/**
	 * Initiate this class as a new object
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $data
	 * @return  static 
	 */
	public static function make($uri, $data = array())
	{
		$segments = explode(' ', $uri);
		$type     = 'GET';

		if (in_array(strtoupper($segments[0]), array('DELETE', 'POST', 'PUT', 'GET'))) 
		{
			$uri  = $segments[1];
			$type = $segments[0];
		}
		else
		{
			throw new Exception(__METHOD__.": Provided {$uri} can't be processed.");
		}

		$data = array_merge(static::query_string($uri), $data);

		return new static($uri, $data, $type);
	}

	/**
	 * A shortcode to initiate this class as a new object using GET
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $data
	 * @return  static 
	 */
	public static function get($uri, $data = array())
	{
		$data = array_merge(static::query_string($uri), $data);
		
		return new static($uri, $data, 'GET');
	}
	
	/**
	 * A shortcode to initiate this class as a new object using POST
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $data
	 * @return  static 
	 */
	public static function post($uri, $data = array())
	{
		return new static($uri, $data, 'POST');
	}
	
	/**
	 * A shortcode to initiate this class as a new object using PUT
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $data
	 * @return  static 
	 */
	public static function put($url, $data = array())
	{
		return new static($uri, $data, 'PUT');
	}
	
	/**
	 * A shortcode to initiate this class as a new object using DELETE
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $data
	 * @return  static 
	 */
	public static function delete($url, $data = array())
	{
		return new static($uri, $data, 'DELETE');
	}
	
	/**
	 * Generate query string
	 * 
	 * @static
	 * @access  protected
	 * @param   string  $uri
	 * @return  array 
	 */
	protected static function query_string($uri)
	{
		$query_data   = array();
		$query_string = parse_url($uri);
		
		if (isset($query_string['query'])) 
		{
			$uri = $query_string['path'];
			parse_str($query_string['query'], $query_data);
		}
		
		return $query_data;
	}
	
	protected $request_uri    = '';
	protected $adapter        = null;
	protected $request_data   = array();
	protected $request_method = '';
	
	/**
	 * Construct a new object
	 * 
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $data
	 * @param   string  $type 
	 */
	public function __construct($uri, $data = array(), $type = 'GET')
	{
		if ( ! function_exists('curl_init'))
		{
			throw new Exception(__METHOD__.": curl_init() is not available.");
		}

		$this->request_uri    = $uri;
		$this->request_method = $type;
		$this->request_data   = $data;
		$this->adapter        = curl_init();

		$option = array();

		switch ($type)
		{
			case 'GET' :
				$option[CURLOPT_HTTPGET] = true;
			break;

			case 'PUT' :
				$data = (is_array($data) ? http_build_query($data) : $data);
				$option[CURLOPT_CUSTOMREQUEST]  = 'PUT';
				$option[CURLOPT_RETURNTRANSFER] = true;
				$option[CURLOPT_HTTPHEADER]     = array('Content-Type: '.strlen($data));
				$option[CURLOPT_POSTFIELDS]     = $data;
			break;
			
			case 'POST' :
				$option[CURLOPT_POST]       = true;
				$option[CURLOPT_POSTFIELDS] = $data;
			break;   
		}

		$this->option($option);
	}
	
	/**
	 * Set curl options
	 * 
	 * @access  public
	 * @param   mixed   $name
	 * @param   string  $value
	 * @return  Curl 
	 */
	public function option($name, $value = null)
	{
		if (is_array($name))
		{
			curl_setopt_array($this->adapter, $name);
		}
		elseif (is_string($name) and isset($value))
		{
			curl_setopt($this->adapter, $name, $value);
		}
		
		return $this;
	}

	/**
	 * Enable curl options through setter
	 *
	 * @access  public
	 * @param   string   $key
	 * @param   string   $value
	 */
	public function __set($key, $value) 
	{
		$this->option($key, $value);
	}
	
	/**
	 * Execute the Curl request and return the output
	 * 
	 * @access  public
	 * @return  object
	 */
	public function call()
	{
		$uri = $this->request_uri.'?'.http_build_query($this->request_data, '', '&');
		$this->option(CURLOPT_URL, $uri); 
		
		$info = curl_getinfo($this->adapter);
		
		$response         = new stdClass();
		$response->body   = $response->raw_body = curl_exec($this->adapter);
		$response->status = $info['http_code'];
		$response->info   = $info;
		
		// clean up curl session
		curl_close($this->adapter);
		
		return $response;
	}

}