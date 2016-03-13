<?php

namespace MushroomFramework\InputOutput;
use \Exception;

class Cookie extends InputOutput {
	protected static $source;

	protected function __construct() {
		static::$source = &$_COOKIE;
	}

	public function set($key, $value, $lifeTime=null) {
		if($lifeTime === null) {
			setcookie($key, $value);
		} else {
			setcookie($key, $value, time()+$lifeTime);
		}
		$_COOKIE[$key] = $value;
	}

	public function remove($key) {
		unset($_COOKIE[$key]);
		setcookie($key);
	}
}
