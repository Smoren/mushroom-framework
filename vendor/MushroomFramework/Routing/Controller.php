<?php

namespace MushroomFramework\Routing;
use \Exception;

class Controller {
	function __construct() {
		
	}

	public static function make($name) {
		$className = ucfirst($name).'Controller';
		$fileName = "{$className}.php";
		if(is_file(MUSHROOM_DIR_APP_CONTROLLERS.'/'.$fileName)) {
			throw new Exception("file '$fileName' already exists");
		}
		$classString = "<?php\n\nclass $className extends Controller {\n\tpublic function index() {\n\t\t\n\t}\n}";
		file_put_contents(MUSHROOM_DIR_APP_CONTROLLERS.'/'.$fileName, $classString);
		return $fileName;
	}
}