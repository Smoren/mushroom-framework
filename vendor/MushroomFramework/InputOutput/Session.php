<?php

namespace MushroomFramework\InputOutput;
use \Exception;

class Session extends InputOutput {
	protected static $source;
	protected static $instance;

	protected function __construct() {
		static::$source = &$_SESSION;
	}
}
