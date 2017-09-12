<?php

namespace MushroomFramework\ORMushroom\Base;
use \MushroomFramework\ORMushroom;
use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionException;

abstract class SessionDecorator {
	protected static $databaseSession;
	protected $_databaseSession;
	protected $_decoratedObject;

	static public function init(ORMushroom\DatabaseSession $databaseSession) {
		static::$databaseSession = $databaseSession;
	}

	static public function _getDecoratedClassName(ORMushroom\DatabaseSession $databaseSession=null, $className=null) {
		if(!$className) {
			$className = get_called_class();
		}

		if(!$databaseSession && static::$databaseSession) {
			$databaseSession = static::$databaseSession;
		} elseif(!static::$databaseSession) {
			throw new ReflectionException($className.'::init() call required');
		}
		$arClassName = explode('\\', $className);
		$className = array_pop($arClassName);
		return $databaseSession->getFullClassName($className);
	}

	static public function _getDatabaseSession(array &$args) {
		if(sizeof($args) && $args[0] instanceof ORMushroom\DatabaseSession) {
			return array_shift($args);
		} elseif(static::$databaseSession) {
			return static::$databaseSession;
		} else {
			throw new ReflectionException(get_called_class().'::init() call required');
		}
	}

	function __construct(...$args) {
		$this->_databaseSession = static::_getDatabaseSession($args);
		$className = static::_getDecoratedClassName($this->_databaseSession);

		$reflection = new ReflectionClass($className); 
		if($reflection->getConstructor()) {
			$this->_decoratedObject = $reflection->newInstanceArgs($args);
		} else {
			$this->_decoratedObject = $reflection->newInstance();
		}
	}

	static function __callStatic($methodName, array $args) {
		$self = new static;
		$self->_databaseSession = static::_getDatabaseSession($args);
		$className = static::_getDecoratedClassName($self->_databaseSession);

		$method = new ReflectionMethod($className, $methodName);
		if($method->isStatic() && $method->isPublic()) {
			$result = call_user_func_array(array($className, $methodName), $args);
			if($result instanceof $className) {
				$self->_decoratedObject = $result;
				return $self;
			} else {
				return $result;
			}
		} else {
		 	throw new ReflectionException("Method '$className::$methodName' is not static public");
		}
	}

	function __call($methodName, array $args) {
		$className = static::_getDecoratedClassName($this->_databaseSession);
		if(!static::$databaseSession) {
			throw new ReflectionException("$className::init() call required");
		}
		if(!$this->_decoratedObject) {
		 	throw new ReflectionException("Nothing to decorate");
		}
		$method = new ReflectionMethod($this->_decoratedObject, $methodName);
		if($method->isPublic()) {
			$result = call_user_func_array(array($this->_decoratedObject, $methodName), $args);
			if($result instanceof $className) {
				$this->_decoratedObject = $result;
				return $this;
			} else {
				return $result;
			}
		} else {
		 	throw new ReflectionException("Method '".static::$className."::$methodName' is not public");
		}
	}
}