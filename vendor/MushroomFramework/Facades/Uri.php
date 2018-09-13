<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class Uri
 * @package MushroomFramework\Facades
 * @method make($addr, ...$data) static
 */
class Uri extends Facade {
	protected static $locClassName = '\\MushroomFramework\\Routing\\Uri';
}