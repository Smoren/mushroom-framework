<?php

namespace MushroomFramework\Pattern;

class Singleton {
	protected static $instance;

    protected function __construct() {

	}

	public static function gi() {
		if(!static::$instance) {
			static::$instance = new static();
		}
		return static::$instance;
	}
}