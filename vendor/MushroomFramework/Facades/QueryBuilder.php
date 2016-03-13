<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

class QueryBuilder extends Facade {
	protected static $locClassName = '\\MushroomFramework\\Database\\Mysql\\QueryBuilder';
	public static function setDatabaseType($type) {
		$type = ucfirst(mb_strtolower($type));
		static::setLocClassName("\\MushroomFramework\\Database\\$type\\QueryBuilder");
	}
}