<?php namespace Hybrid;

use Laravel\HTML as Laravel_HTML;

class HTML extends Laravel_HTML 
{
	/**
	 * Generate a HTML element
	 *
	 * @static
	 * @access public
	 * @param  string $tag
	 * @param  mixed  $value
	 * @param  array  $attributes
	 * @return string
	 */
	public static function create($tag = 'div', $value = null, $attributes = array())
	{
		if (is_array($value))
		{
			$attributes = $value;
			$value      = null;
		}

		$content = '<'.$tag.static::attributes($attributes).'>';

		if ( ! is_null($value))
		{
			$content .= static::entities($value).'</'.$tag.'>';
		}
		
		return $content;
	}	
}