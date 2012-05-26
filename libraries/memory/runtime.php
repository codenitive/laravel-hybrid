<?php namespace Hybrid\Memory;

class Runtime extends Driver
{
	/**
	 * @access  protected
	 * @var     string  storage configuration, currently only support runtime.
	 */
	protected $storage = 'runtime';

	public function initiate() { }

	public function shutdown() { }

}