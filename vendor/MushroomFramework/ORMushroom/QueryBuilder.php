<?php

namespace MushroomFramework\ORMushroom;
use \MushroomFramework\ORMushroom\Base;

/**
 * QueryBuilder Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class QueryBuilder {
	protected $_decoratedObject;
	protected $_dbSession;

	public static function __callStatic($methodName, $args) {
		list($className, $dbSession) = static::init($args);

		// запускаем статический метод класса
		$result = call_user_func_array(array($className, $methodName), $args);

		if($result instanceof Base\QueryBuilder) {
			// декорируем объект
			$qb = new static;
			$qb->_dbSession = $dbSession;
			$qb->_decoratedObject = $result;
			
			return $qb;
		} else {
			return $result;
		}
	}

	public static function init(&$args, QueryBuilder $qb=null) {
		// если первым агрументом метода является объект DatabaseSession
		if(sizeof($args) && $args[0] instanceof DatabaseSession) {
			// запоминаем его и убираем его из аргументов
			$dbSession = array_shift($args);
		} else {
			// забираем дефолтный объект DatabaseSession либо null, если его нет
			$dbSession = DatabaseSession::getDefaultSession();
		}

		// если объект сессии найден
		if($dbSession) {
			// берем из него пространство имен библиотеки используемой СУБД
			$namespace = $dbSession->getNamespace();
		} else {
			// иначе берем пространство имен базовой библиотеки
			$namespace = DatabaseSession::getBaseNamespace();
		}

		$className = $namespace.'\\QueryBuilder';

		if($qb) {
			$qb->_dbSession = $dbSession;
			$qb->_decoratedObject = new $className;

			return $qb;
		}

		return array($className, $dbSession);
	}

	public static function create(&$args) {
		list($className, $dbSession) = static::init($args);
		$qb = new static;
		$qb->_dbSession = $dbSession;
		$qb->_decoratedObject = new $className;
		
		return $qb;
	}

	public function exec() {
		return $this->_decoratedObject->exec($this->_dbSession);
	}

	public function getDatabaseSession() {
		return $this->_dbSession;
	}

	public function __call($methodName, $args) {
		if(!$this->_decoratedObject) {
			return static::__callStatic($methodName, $args);
		}

		$result = $this->_decoratedObject->$methodName(...$args);
		if($result instanceof Base\QueryBuilder) {
			return $this;
		} else {
			return $result;
		}
	}

	public function __toString() {
		return $this->_decoratedObject->__toString();
	}
}
