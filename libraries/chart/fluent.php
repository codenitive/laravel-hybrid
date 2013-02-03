<?php namespace Hybrid\Chart;

use Hybrid\Chart as Chart;

class Fluent {
	
	/**
	 * Make a new instance.
	 *
	 * @static
	 * @access public
	 * @return self
	 */
	public static function make()
	{
		return new static();
	}

	/**
	 * Column values stored as array
	 *
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Rows values stored as array
	 *
	 * @var array
	 */
	protected $rows = array();

	/**
	 * H-axis value
	 *
	 * @var string
	 */
	protected $h_axis = 'string';

	/**
	 * Setter for columns
	 *
	 * @access public
	 * @param  array    $data
	 * @return self
	 */
	public function set_columns($data = array())
	{
		$this->columns = array();
		$count = 0;

		if (count($data) > 0)
		{
			foreach ($data as $key => $value)
			{
				if ($count === 0) $this->h_axis = $value;

				if (is_numeric($key)) $key = 'string';

				$this->columns[] = "data.addColumn('{$value}', '{$key}');";
				$count++;
			}
		}

		return $this;
	}

	/**
	 * Setter for rows
	 *
	 * @access public
	 * @param  array    $data
	 * @return self
	 */
	public function set_rows($data = array())
	{
		$dataset = array();
		$h_axis = isset($this->h_axis) ? $this->h_axis : null;

		$x = 0;
		$y = 0;

		if (count($data) > 0)
		{
			foreach ($data as $key => $value)
			{
				$key = ($h_axis == 'date' ? $this->parse_date($key) : sprintf("'%s'", $key));
				$dataset[] = "data.setValue({$x}, {$y}, ".$key.");";

				foreach ($value as $k => $v)
				{
					$y++;
					$dataset[] = "data.setValue({$x}, {$y}, {$v});";
				}

				$x++;
				$y = 0;
			}
		}

		array_unshift($dataset, "data.addRows(".$x.");");
		$this->rows = $dataset;

		return $this;
	}

	/**
	 * Getter for columns
	 *
	 * @access public
	 * @return string
	 */
	public function get_columns()
	{
		return implode("\r\n", $this->columns);
	}

	/**
	 * Getter for rows
	 *
	 * @access public
	 * @return string
	 */
	public function get_rows()
	{
		return implode("\r\n", $this->rows);
	}

	/**
	 * Parse PHP Date Object into JavaScript new Date() format
	 *
	 * @access protected
	 * @param  DateTime $date
	 * @return string
	 */
	protected function parse_date(DateTime $date)
	{
		$key = strtotime($date);
		return 'new Date('.date('Y', $key).', '.(date('m', $key) - 1).', '.date('d', $key).')';
	}

	/**
	 * Export to a chart.
	 *
	 * @access public 	
	 * @param  string   $name
	 * @return Driver
	 */
	public function export($name)
	{
		return Chart::make($name, $this);
	}
}