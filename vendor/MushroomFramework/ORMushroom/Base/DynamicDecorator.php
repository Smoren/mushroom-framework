<?php

namespace MushroomFramework\ORMushroom\Base;
use \MushroomFramework\ORMushroom\Decorators\DatabaseManager;
use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionException;

abstract class DynamicDecorator {
	protected $obj;

	protected function init($obj) {
		$this->obj = $obj;
		return $this->obj;
	}

	function __call($methodName, array $args) {
		if(!$this->obj) {
		 	throw new ReflectionException("Nothing to decorate");
		}
		$method = new ReflectionMethod($this->obj, $methodName);
		if($method->isPublic()) {
			$result = call_user_func_array(array($this->obj, $methodName), $args);
			if(is_object($result) && get_class($result) === get_class($this->obj)) {
				$this->obj = $result;
				return $this;
			} else {
				return $result;
			}
		} else {
		 	throw new ReflectionException("Method '".static::$className."::$methodName' is not public");
		}
	}
}