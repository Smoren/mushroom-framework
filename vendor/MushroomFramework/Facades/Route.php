<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class Route
 * @package MushroomFramework\Facades
 * @method register($mask, $addr) static
 * @method rest($mask, $controllerName, $idRegExp='[0-9]+') static
 * @method error($exceptionName, $addr) static
 * @method setMask($mask) static
 * @method setAddr($addr) static
 * @method go(Router $router) static
 * @method getController() static
 * @method getAction() static
 * @method getAddr() static
 * @method getArgsSize() static
 * @method getUri(Array $data) static
 * @method addToTree(Array &$tree) static
 */
class Route extends Facade {
	protected static $locClassName = '\\MushroomFramework\\Routing\\Route';
}