<?php

namespace MushroomFramework\Patterns;
use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionException;

abstract class Decorator {
	protected static $className;
	protected $obj;

	static public function setClassName($className) {
		static::$className = $className;
	}
	
	function __construct(...$args) {
		$reflection = new ReflectionClass(static::$className); 
		$this->obj = $reflection->newInstanceArgs($args); 
	}

	function __callStatic($methodName, array $args) {
		$self = new static();
		$method = new ReflectionMethod(static::$className, $methodName);
		if($method->isStatic() && $method->isPublic()) {
			$result = call_user_func_array(array(static::$className, $methodName), $args);
			$className = static::$className;
			if($result instanceof $className) {
				$self->obj = $result;
				return $self;
			} else {
				return $result;
			}
			$self->obj = call_user_func_array(array(static::$className, $methodName), $args);
		} else {
		 	throw new ReflectionException("Method '".static::$className."::$methodName' is not static public");
		}
	}

	function __call($methodName, array $args) {
		if(!$this->obj) {
		 	throw new ReflectionException("Nothing to decorate");
		}
		$method = new ReflectionMethod($this->obj, $methodName);
		if($method->isPublic()) {
			$result = call_user_func_array(array($this->obj, $methodName), $args);
			$className = static::$className;
			if($result instanceof $className) {
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