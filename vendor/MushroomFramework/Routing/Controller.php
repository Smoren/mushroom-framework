<?php

namespace MushroomFramework\Routing;
use \Exception;

class Controller {
	public static function make($name) {
		$className = ucfirst($name).'Controller';
		$fileName = "{$className}.php";
		if(is_file(MUSHROOM_DIR_APP_CONTROLLERS.'/'.$fileName)) {
			throw new Exception("file '$fileName' already exists");
		}
		$classString = "<?php\n\nclass $className extends Controller {\n\tpublic function getIndex() {\n\t\t\n\t}\n}";
		file_put_contents(MUSHROOM_DIR_APP_CONTROLLERS.'/'.$fileName, $classString);
		return $fileName;
	}

	function __call($methodName, $args=array()) {
		if(is_callable(array($this, $methodName))) {
			return call_user_func_array(array($this, $methodName), $args);
		} else {
			throw new Exception('In controller '.get_called_class().' method '.$methodName.' not found!');
		}
	}
}