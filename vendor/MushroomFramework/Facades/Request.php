<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class Request
 * @package MushroomFramework\Facades
 * @method getMethod() static
 * @method isAjax() static
 * @method input($key=null, $default=null) static
 * @method only(...$keys) static
 * @method except(...$data) static
 * @method get($key=null, $default=null) static
 * @method post($key=null, $default=null) static
 * @method cookie($key=null, $default=null) static
 * @method json($key=null, $default=null) static
 * @method all() static

 */
class Request extends Facade {
	protected static $locClassName = '\\MushroomFramework\\InputOutput\\Request';
}