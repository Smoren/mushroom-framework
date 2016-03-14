<?php

namespace MushroomFramework\InputOutput;
use \MushroomFramework\InputOutput\Exceptions\EventException;
use Closure;

class Event {
	protected static $handlers;

	public static function register($name, Closure $handler) {
		if(!is_array(static::$handlers)) {
			static::$handlers = array();
		}
		if(!isset(static::$handlers[$name]) || !is_array(static::$handlers[$name])) {
			static::$handlers[$name] = array();
		}
		static::$handlers[$name][] = $handler;
	}

	public static function trigger($name, &$data=null) {
		if(!is_array(static::$handlers) || !isset(static::$handlers[$name]) || !is_array(static::$handlers[$name]) || !sizeof(static::$handlers[$name])) {
			throw new EventException("Handlers for event '$name' are not found");
		}
		foreach(static::$handlers[$name] as $handler) {
			$handler($data);
		}
	}

	public static function triggerSilent($name, &$data=null) {
		try {
			static::trigger($name, $data);
		} catch(EventException $e) {
			
		}
	}
}
