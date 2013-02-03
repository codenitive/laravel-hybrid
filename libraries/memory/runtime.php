<?php namespace Hybrid\Memory;

/**
 * Runtime Memory class
 *
 * @package    Hybrid\Memory
 * @category   Runtime
 * @author     Laravel Hybrid Development Team
 */

class Runtime extends Driver {

	/**
	 * Storage name
	 * 
	 * @access  protected
	 * @var     string  
	 */
	protected $storage = 'runtime';

	/**
	 * No initialize method for runtime
	 *
	 * @access  public
	 * @return  void
	 */
	public function initiate() 
	{
		return true;
	}

	/**
	 * No shutdown method for runtime
	 *
	 * @access  public
	 * @return  void
	 */
	public function shutdown() 
	{
		return true;
	}
}