<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class Cookie
 * @package MushroomFramework\Facades
 * @method get($key=null, $default=null) static
 * @method only(...$keys) static
 * @method except(...$keys) static
 * @method set($key, $value) static
 * @method remove($key) static
 */
class Cookie extends Facade {
	protected static $locClassName = '\\MushroomFramework\\InputOutput\\Cookie';
}