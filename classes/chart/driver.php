<?php namespace Hybrid;

use \Config, \Exception;

abstract class Chart_Driver 
{
	/**
	 * A shortcode to initiate this class as a new object
	 * 
	 * @static
	 * @access  public
	 * @return  static 
	 */
	public static function make()
	{
		return new static();
	}

	protected $options = array();
	protected $hAxis   = 'string';
	protected $columns = '';
	protected $rows    = '';

	/**
	 * Clean-up private property on new object
	 * 
	 * @access  public
	 */
	public function __construct() 
	{
		$this->clear();
	}

	/**
	 * Run the clean-up
	 * 
	 * @access  public
	 * @return  bool
	 */
	public function clear() 
	{
		$this->options = array();
		$this->columns = '';
		$this->rows    = '';

		return $this;
	}

	/**
	 * Set columns information
	 * 
	 * @access  public
	 * @param   array   $data 
	 */
	public function set_columns($data = array()) 
	{
		$this->columns = '';
		$count         = 0;

		if (count($data) > 0) 
		{
			foreach ($data as $key => $value) 
			{
				if ($count === 0) $this->hAxis = $value;

				if (is_numeric($key)) $key = 'string';
				
				$this->columns .= "data.addColumn('{$value}', '{$key}');\r\n";
				$count++;
			}
		}

		return $this;
	}

	/**
	 * Set chart options
	 * 
	 * @access  public
	 * @param   mixed   $name
	 * @param   mixed   $value
	 * @return  bool
	 */
	public function set_options($name, $value = '') 
	{
		if (is_array($name)) 
		{
			foreach ($name as $key => $value) 
			{
				$this->options[$key] = $value;
			}
		}
		elseif (is_string($name) and ! empty($name)) 
		{
			$this->options[$name] = $value;
		}
		else
		{
			throw new FuelException(__FUNCTION__.': require \$name to be set.');
		}

		return $this;
	}

	/**
	 * Set rows information
	 * 
	 * @access  public
	 * @param   array   $data 
	 */
	public function set_rows($data = array()) 
	{
		$this->rows = "";
		$dataset    = '';

		$x = 0;
		$y = 0;

		if (count($data) > 0) 
		{
			foreach ($data as $key => $value) 
			{
				if ($this->hAxis == 'date') $key = $this->parse_date($key);
				else $key = sprintf("'%s'", $key);

				$dataset .= "data.setValue({$x}, {$y}, ".$key.");\r\n";

				foreach ($value as $k => $v) 
				{
					$y++;
					$dataset .= "data.setValue({$x}, {$y}, {$v});\r\n";
				}

				$x++;
				$y = 0;
			}
		}
		
		$this->rows .= "data.addRows(".$x.");\r\n{$dataset}";

		return $this;
	}

	/**
	 * Parse PHP Date Object into JavaScript new Date() format
	 * 
	 * @access  protected
	 * @param   date    $date
	 * @return  string 
	 */
	protected function parse_date($date) 
	{
		$key = strtotime($date);
		return 'new Date('.date('Y', $key).', '.(date('m', $key) - 1).', '.date('d', $key).')';
	}

	/**
	 * Render self
	 *
	 * @abstract
	 * @access  public
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Render the chart
	 * 
	 * @abstract
	 * @access  public
	 * @param   int     $width
	 * @param   int     $height
	 */
	public abstract function render($width, $height);
	
}