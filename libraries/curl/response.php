<?php namespace Hybrid\Curl;

class Response
{
	public static function make($raw_body = '', $http_code = 200) 
	{
		return new static($raw_body, $http_code);
	}

	public function __construct($raw_body = '', $http_code = 200) 
	{
		$this->body     = $raw_body;
		$this->raw_body = $raw_body;
		$this->status   = $http_code;
	}

	protected $responses = array();

	public function __set($key, $value = null) 
	{
		if (is_string($key)) $this->responses[$key] = $value;
	}

	public function __get($key)
	{
		if (isset($this->responses[$key])) return $this->responses[$key];
	}

	public function __isset($key) 
	{
		return isset($this->responses[$key]);
	}
}