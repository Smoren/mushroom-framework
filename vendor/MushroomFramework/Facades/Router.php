<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class Router
 * @package MushroomFramework\Facades
 * @method getRequestMethod() static
 * @method addRoute(Route $route) static
 * @method addError($exceptionName, Route $route) static
 * @method getRoutes() static
 * @method findRoute($addr, $argsSize) static
 * @method throwStatusException($code, $message) static
 * @method transfer($where, $data=array(), $decorator=null) static
 * @method getError($code=404) static
 * @method getMethod() static
 * @method setMethod($method) static
 * @method checkMethod($method) static
 */
class Router extends Facade {
	protected static $locClassName = '\\MushroomFramework\\Routing\\Router';
}