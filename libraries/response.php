<?php namespace Hybrid;

/**
 * Response class
 *
 * @package    Hybrid
 * @category   Response
 * @author     Laravel Hybrid Development Team
 */

use \File, \Input, \View,
	Laravel\Response as R;

class Response extends R {

	/**
	 * Create a new RESTful response or View response depending on requested 
	 * format.
	 * 
	 * @param  array        $data   
	 * @param  integer      $status 
	 * @return Response      
	 */
	public static function restful($data = array(), $status = 200)
	{
		$format     = Input::get('format');
		$collection = Format::make($data['data']);

		if (is_null($format) or ! method_exists($collection, 'to_'.$format))
		{
			// Response with an instanceof view, or a possible view.
			if (isset($data['view']))
			{
				$view = $data['view'];

				// Initiate a View instance if $view is not.
				if ( ! ($view instanceof View)) $view = View::make($view);

				$view->with($data['data']);

				return static::make($view, $status);
			}
			else
			{
				// we should send an 500 server error for request that doesn't 
				// have a proper response format
				return static::error('500');
			}
		}
		else 
		{
			$response = $collection->{'to_'.$format}();
			$headers  = array('Content-Type' => File::mime($format));

			return Response::make($response, $status, $headers);
		}
	}
}