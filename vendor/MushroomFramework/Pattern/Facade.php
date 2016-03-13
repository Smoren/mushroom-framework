<?php

namespace MushroomFramework\Pattern;
use \ReflectionMethod;
use \ReflectionException;

abstract class Facade {
	protected static $locClassName;

	public static function __callStatic($methodName, array $params) {
		$method = new ReflectionMethod(static::$locClassName, $methodName);
		if($method->isStatic()) {
			return call_user_func_array(array(static::$locClassName, $methodName), $params);
		} elseif(method_exists(static::$locClassName, 'gi')) {
			$instance = call_user_func_array(array(static::$locClassName, 'gi'), array());
			return call_user_func_array(array($instance, $methodName), $params);
		} else {
		 	throw new ReflectionException("Method '".static::$locClassName."::$methodName' is not static");
		}
	}

	public function call($name, $args) {
		return call_user_func_array(array(static::$locClassName, $name), $args);
	}

	public static function setLocClassName($className) {
		if(!class_exists($className)) {
			throw new ReflectionException("Class '$className' doesn't exist");
		}
		static::$locClassName = $className;
	}
}