<?php

namespace MushroomFramework\ORMushroom;
use \MushroomFramework\ORMushroom\Base;
use \Exception;
use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionException;

/**
 * DatabaseSession Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class DatabaseSession {
	protected static $defaultSession;
	protected $namespace;
	protected $obj;

	public static function setDefaultSession(DatabaseSession $dbSession) {
		static::$defaultSession = $dbSession;
	}

	public static function getDefaultSession() {
		if(!static::$defaultSession) {
			throw new \Exception('No default DatabaseSession object');
		}
		return static::$defaultSession;
	}

	public static function getDefaultNamespace() {
		return "\\MushroomFramework\\ORMushroom\\Base";
	}

	public function getNamespace() {
		return $this->namespace;
	}

	public function getFullClassName($className) {
		return "{$this->namespace}\\{$className}";
	}

	function __construct($dbConfig, $isDefaultSession=false) {
		$this->namespace = "\\MushroomFramework\\ORMushroom\\".\ucfirst(\strtolower($dbConfig['type']));

		// инициализируем основной объект
		$className = $this->getFullClassName('DatabaseSession');
		$this->init(new $className($dbConfig));

		if($isDefaultSession) {
			static::setDefaultSession($this);
		}
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

	function __callStatic($methodName, array $args) {
		static::getDefaultSession()->$methodName();
	}

	protected function init($obj) {
		$this->obj = $obj;
		return $this->obj;
	}
}


