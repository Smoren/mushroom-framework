<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;
use \MushroomFramework\View\View;

/**
 * Class Response
 * @package MushroomFramework\Facades
 * @method status($statusCode) static
 * @method error($statusCode, $message='') static
 * @method mimeType($type=null) static
 * @method attachmentName($name) static
 * @method addData($name, $val) static
 * @method text($str) static
 * @method json($data) static
 * @method file() static
 * @method genFile($str, $name, $ext=null) static
 */
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