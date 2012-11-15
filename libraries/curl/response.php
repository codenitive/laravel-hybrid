<?php namespace Hybrid\Curl;

/**
 * Curl Response class
 *
 * @package    Hybrid\Curl
 * @category   Response
 * @author     Laravel Hybrid Development Team
 */

class Response {
	
	/**
	 * Make new Curl\Response instance
	 *
	 * @static
	 * @access public
	 * @param  string  $raw_body 
	 * @param  integer $http_code
	 * @return Response
	 */
	public static function make($raw_body = '', $http_code = 200) 
	{
		return new static($raw_body, $http_code);
	}

	/**
	 * Return a new Curl\Response instance
	 * 
	 * @access public
	 * @param  string  $raw_body 
	 * @param  integer $http_code
	 * @return Response
	 */
	public function __construct($raw_body = '', $http_code = 200) 
	{
		$this->responses['body']     = $raw_body;
		$this->responses['raw_body'] = $raw_body;
		$this->responses['status']   = $http_code;
	}

	/**
	 * Responses data collection
	 *
	 * @access protected
	 * @var    array
	 */
	protected $responses = array();

	/**
	 * Magic Method for handling dynamic data access.
	 */
	public function __set($key, $value = null) 
	{
		if (is_string($key)) $this->responses[$key] = $value;
	}

	/**
	 * Magic Method for handling the dynamic setting of data.
	 */
	public function __get($key)
	{
		if (isset($this->responses[$key])) return $this->responses[$key];
	}

	/**
	 * Magic Method for checking dynamically-set data.
	 */
	public function __isset($key) 
	{
		return isset($this->responses[$key]);
	}
}