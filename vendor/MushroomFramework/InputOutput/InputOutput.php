<?php

namespace MushroomFramework\InputOutput;
use \MushroomFramework\Pattern\Singleton;
use \Exception;

/**
 * Class InputOutput
 * @package MushroomFramework\InputOutput
 */
abstract class InputOutput extends Singleton {
	protected static $source;
	protected static $instance;

	protected function __construct() {
		static::$source = array();
	}

	public function get($key=null, $default=null) {
		if(!$key) return static::$source;
		if(!isset(static::$source[$key]) && $default === null) {
			throw new Exception("'$key' is not defined");
		}
		return isset(static::$source[$key]) ? static::$source[$key] : $default;
	}

	public function only(...$keys) {
		$res = array();
		foreach($keys as $key) {
			if(!isset(static::$source[$key])) {
				$res[$key] = null;
			} else {
				$res[$key] = static::$source[$key];
			}
		}
		return $res;
	}

	public function except(...$keys) {
		$res = array();
		foreach(static::$source[$key] as $key => $val) {
			if(in_array($key, $data)) continue;
			$res[$key] = $val;
		}
		return $res;
	}

	public function set($key, $value) {
		static::$source[$key] = $value;
	}

	public function remove($key) {
		unset(static::$source[$key]);
	}
}
