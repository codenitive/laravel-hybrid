<?php namespace Hybrid;

/**
 * Curl class
 *
 * @package    Hybrid
 * @category   Curl
 * @author     Laravel Hybrid Development Team
 */

class Curl {
	
	/**
	 * Create a new Curl instance
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
	 * @param   string  $url
	 * @param   array   $data
	 * @return  static 
	 */
	public static function put($url, $data = array())
	{
		return new static($url, $data, 'PUT');
	}
	
	/**
	 * A shortcode to initiate this class as a new object using DELETE
	 * 
	 * @static
	 * @access  public
	 * @param   string  $url
	 * @param   array   $data
	 * @return  static 
	 */
	public static function delete($url, $data = array())
	{
		return new static($url, $data, 'DELETE');
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

		$this->adapter        = curl_init($uri);
		$this->option(CURLOPT_URL, $uri);

		switch ($type)
		{
			case 'GET' :
				$this->option(CURLOPT_HTTPGET, 1);
			break;

			case 'PUT' :
				$data = (is_array($data) ? http_build_query($data) : $data);
				$this->option(CURLOPT_CUSTOMREQUEST, 'PUT');
				$this->option(CURLOPT_RETURNTRANSFER, 1);
				$this->option(CURLOPT_HTTPHEADER, array('Content-Type: '.strlen($data)));
				$this->option(CURLOPT_POSTFIELDS, $data);
			break;
			
			case 'POST' :
				$this->option(CURLOPT_POST, 1);
				$this->option(CURLOPT_POSTFIELDS, $data);
			break;   
		}
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
		elseif (isset($value))
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
		$raw_body         = curl_exec($this->adapter);
		$info             = curl_getinfo($this->adapter);
		$response         = Curl\Response::make($raw_body, $info['http_code']);
		$response->info   = $info;
		
		// clean up curl session
		curl_close($this->adapter);
		
		return $response;
	}

}