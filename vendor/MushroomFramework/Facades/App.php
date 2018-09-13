<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class App
 * @package MushroomFramework\Facades
 * @method start() static static
 * @method showError($e) static
 * @method init() static
 * @method getRouter() static
 * @method getConfig() static
 * @method getConfigOption($name=false) static
 * @method getDbInterface() static
 * @method property($name, $value=null) static
 * @method setProperty($name, $value) static
 * @method getProperty($name, $default=null) static
 * @method initModules() static
 */
class App extends Facade {
	protected static $locClassName = '\\MushroomFramework\\Main\\App';
}