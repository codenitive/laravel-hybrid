<?php

namespace Hybrid;

class Memory_Runtime extends Memory_Driver
{
	/**
	 * @access  protected
	 * @var     string  storage configuration, currently only support runtime.
	 */
	protected $storage = 'runtime';

	public function initiate() { }

	public function shutdown() { }
}