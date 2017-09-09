<?php

namespace MushroomFramework\ORMushroom\Facades;
use \ReflectionMethod;
use \ReflectionException;

/**
 * QueryBuilder Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class QueryBuilder {
	protected static $className;
	protected $obj;

	function __callStatic($methodName, array $args) {
		$self = new static();
		$method = new ReflectionMethod(static::$className, $methodName);
		if($method->isStatic()) {
			$self->obj = call_user_func_array(array(static::$className, $methodName), $args);
		} else {
		 	throw new ReflectionException("Method '".static::$className."::$methodName' is not static");
		}
		return $self;
	}

	function __call($methodName, array $args) {
		if(!$this->obj) {
		 	throw new ReflectionException("Nothing to decorate");
		}
		$method = new ReflectionMethod($this->obj, $methodName);
		if($method->isPublic()) {
			$this->obj = call_user_func_array(array(static::$obj, $methodName), $args);
		} else {
		 	throw new ReflectionException("Method '".static::$className."::$methodName' is not public");
		}
	}

	function init($className) {
		static::$className = $className;
	}
}
