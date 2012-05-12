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
	 * @param   array   $dataset
	 * @return  static 
	 */
	public static function make($uri = '', $dataset = array())
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

		$dataset = array_merge(static::query_string($uri), $dataset);

		return new static($uri, $dataset, $type);
	}

	/**
	 * A shortcode to initiate this class as a new object using GET
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $dataset
	 * @return  static 
	 */
	public static function get($uri, $dataset = array())
	{
		$dataset = array_merge(static::query_string($uri), $dataset);
		
		return new static($uri, $dataset, 'GET');
	}
	
	/**
	 * A shortcode to initiate this class as a new object using POST
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $dataset
	 * @return  static 
	 */
	public static function post($uri, $dataset = array())
	{
		return new static($uri, $dataset, 'POST');
	}
	
	/**
	 * A shortcode to initiate this class as a new object using PUT
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $dataset
	 * @return  static 
	 */
	public static function put($url, $dataset = array())
	{
		return new static($uri, $dataset, 'PUT');
	}
	
	/**
	 * A shortcode to initiate this class as a new object using DELETE
	 * 
	 * @static
	 * @access  public
	 * @param   string  $uri
	 * @param   array   $dataset
	 * @return  static 
	 */
	public static function delete($url, $dataset = array())
	{
		return new static($uri, $dataset, 'DELETE');
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
		$query_dataset = array();
		$query_string  = parse_url($uri);
		
		if (isset($query_string['query'])) 
		{
			$uri = $query_string['path'];
			parse_str($query_string['query'], $query_dataset);
		}
		
		return $query_dataset;
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
	 * @param   array   $dataset
	 * @param   string  $type 
	 */
	public function __construct($uri, $dataset = array(), $type = 'GET')
	{
		if ( ! function_exists('curl_init'))
		{
			throw new Exception(__METHOD__.": curl_init() is not available.");
		}

		$this->request_uri    = $uri;
		$this->request_method = $type;
		$this->request_data   = $dataset;
		$this->adapter        = curl_init();

		$option = array();

		switch ($type)
		{
			case 'GET' :
				$option[CURLOPT_HTTPGET] = true;
			break;

			case 'PUT' :
				$dataset = (is_array($dataset) ? http_build_query($dataset) : $dataset);
				$option[CURLOPT_CUSTOMREQUEST]  = 'PUT';
				$option[CURLOPT_RETURNTRANSFER] = true;
				$option[CURLOPT_HTTPHEADER]     = array('Content-Type: '.strlen($dataset));
				$option[CURLOPT_POSTFIELDS]     = $dataset;
			break;
			
			case 'POST' :
				$option[CURLOPT_POST]       = true;
				$option[CURLOPT_POSTFIELDS] = $dataset;
			break;   
		}

		$this->put($option);
	}
	
	/**
	 * Set curl options
	 * 
	 * @access  public
	 * @param   mixed   $option
	 * @param   string  $value
	 * @return  Curl 
	 */
	public function put($option, $value = null)
	{
		if (is_array($option))
		{
			curl_setopt_array($this->adapter, $option);
		}
		elseif (is_string($option) and isset($value))
		{
			curl_setopt($this->adapter, $option, $value);
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
		$this->put($key, $value);
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
		curl_setopt($this->adapter, CURLOPT_URL, $uri); 
		
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