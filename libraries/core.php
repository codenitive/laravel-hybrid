<?php namespace Hybrid;

/**
 * Core class
 *
 * @package    Hybrid
 * @category   Core
 * @author     Laravel Hybrid Development Team
 */

class Core {

	/**
	 * Start Hybrid
	 *
	 * @static
	 * @access  public
	 * @return  void
	 */
	public static function start() {}

	/**
	 * Import file
	 *
	 * @static
	 * @access  public
	 * @param   string      $file_path
	 * @param   string      $folder
	 * @param   string      $scope
	 * @return  void
	 */
	public static function import($file_path, $folder, $scope = 'hybrid') 
	{
		switch ($scope)
		{
			case 'hybrid' :
				$scope = Bundle::path('hybrid');
				break;
			case 'app' :
			case 'sys' :
				$scope = path($scope);
			default :
				$scope = __DIR__.DS;
		}

		$file_path = str_replace('/', DS, $file_path);

		$file = $scope.$folder.DS.$file_path.EXT;

		if (is_file($file))
		{
			include $scope.$folder.DS.$file_path.EXT;
		}
	}
}