<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class Event
 * @package MushroomFramework\Facades
 * @method register($name, \Closure $handler) static
 */
class Event extends Facade {
	protected static $locClassName = '\\MushroomFramework\\InputOutput\\Event';

	public function trigger($name, &$data=null) {
		$className = static::$locClassName;
		$className::trigger($name, $data);
	}
}