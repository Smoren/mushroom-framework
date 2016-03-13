<?php

namespace MushroomFramework\Pattern;

class Singleton {
	private static $instance;

	private function __construct() {

	}

	public static function gi() {
		if(!static::$instance) {
			static::$instance = new static();
		}
		return static::$instance;
	}
}