<?php namespace Hybrid;

use \Closure, \Exception;

class InvalidArgumentException extends Exception {}
class OutOfBoundsException extends Exception {}
class RuntimeException extends Exception {}

class Core
{
	public static function start() { }

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

		$file_path = str_replace('/', DS, $path);

		$file = $scope.$folder.DS.$file_path.EXT;

		if (is_file($file))
		{
			require_once $scope.$folder.DS.$file_path.EXT;
		}
	}
}