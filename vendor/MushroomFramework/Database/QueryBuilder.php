<?php

namespace MushroomFramework\Database;
use \ReflectionMethod;
use \ReflectionException;

abstract class QueryBuilder {
	protected static $className = 'MushroomFramework\\Database\\';

	public static function __callStatic($methodName, array $params) {
		$method = new ReflectionMethod(static::$className, $methodName);
		if($method->isStatic()) {
			return call_user_func_array(array(static::$className, $methodName), $params);
		} elseif(method_exists(static::$className, 'gi')) {
			$instance = call_user_func_array(array(static::$className, 'gi'), array());
			return call_user_func_array(array($instance, $methodName), $params);
		} else {
		 	throw new ReflectionException("Method '".static::$className."::$methodName' is not static");
		}
	}

	public function call($name, $args) {
		return call_user_func_array(array(static::$className, $name), $args);
	}

	public static function setup($dbm) {
		static::$className .= ucfirst($dbm->type)."\\QueryBuilder";
		if(!class_exists(static::$className)) {
			throw new ReflectionException("Class '".static::$className."' doesn't exist");
		}
		static::setDatabaseManager($dbm);
		static::setEncoding($dbm->encoding, $dbm->collate);
	}
}