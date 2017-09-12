<?php

namespace MushroomFramework\ORMushroom;
use \MushroomFramework\ORMushroom\Base;
use \Exception;

/**
 * DatabaseSession Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class DatabaseSession extends Base\DynamicDecorator {
	protected static $defaultSession;
	protected $dbTypeNamespace;

	public static function setDefaultSession(DatabaseSession $dbSession) {
		static::$defaultSession = $dbSession;
		QueryBuilder::init(static::$defaultSession);
	}

	public static function getDefaultSession() {
		if(!static::$defaultSession) {
			throw new \Exception('No default DatabaseSession object');
		}
		return static::$defaultSession;
	}

	function __construct($dbConfig, $isDefaultSession=false) {
		$this->dbTypeNamespace = "\\MushroomFramework\\ORMushroom\\".\ucfirst(\strtolower($dbConfig['type']));

		// инициализируем основной объект
		$className = $this->getFullClassName('DatabaseSession');
		$this->init(new $className($dbConfig));

		if($isDefaultSession) {
			static::setDefaultSession($this);
		}
	}

	public function getFullClassName($className) {
		return "{$this->dbTypeNamespace}\\{$className}";
	}
}


