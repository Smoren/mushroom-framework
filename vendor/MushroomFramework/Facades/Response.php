<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;
use \MushroomFramework\View\View;

class Response extends Facade {
	protected static $locClassName = '\\MushroomFramework\\InputOutput\\Response';

	public static function redirect($addr, $data=false, $params=false) {
		if(!is_array($data)) {
			$data = array();
		}
		if(!preg_match('/^http[s]{0,1}\:\/\//', $addr)) {
			$addr = Uri::make($addr, $data);
			if(is_array($params) && sizeof($params)) {
				$addr = $addr->withParams($params);
			}
		}
		return static::call('redirect', array($addr, $name, $data, $params, $htmlAttrs));
	}

	public static function view($viewPath) {
		$view = new View();
		return static::call('view', array($view, $viewPath));
	}
}