<?php

namespace MushroomFramework\InputOutput;
use \Exception;
use \Closure;

class Response {
	protected static $globalData = array();
	protected $callback;
	protected $data=array();

	function __construct(Closure $callback) {
		$this->callback = $callback;
		foreach(static::$globalData as $name => $val) {
			$this->with($name, $val);
		}
	}

	public function make() {
		return $this->callback->__invoke($this->data);
	}

	public function with($key, $val) {
		$this->data[$key] = $val;
		return $this;
	}

	public static function status($statusCode) {
		http_response_code($statusCode);
	}

	public static function mimeType($type=null) {
		if(!$type) {
			$type = 'text/plain';
		}
		header("Content-Type: $type");
	}

	public static function attachmentName($name) {
		header("Content-Disposition: attachment; filename=\"$name\"");
	}

	public static function addData($name, $val) {
		static::$globalData[$name] = $val;
	}

	public static function text($str) {
		return new static(function() use ($str) {
			static::mimeType('text/plain');
			return $str;
		});
	}

	public static function view($viewObj, $viewPath) {
		return new static(function($data) use ($viewObj, $viewPath) {
			static::mimeType('text/html');
			$viewObj->init($viewPath);
			return $viewObj->make($data);
		});
	}

	public static function json($data) {
		return new static(function() use ($data) {
			static::mimeType('application/json');
			return json_encode($data);
		});
	}

	// TODO: реализовать
	public static function file() {

	}

	public static function genFile($str, $name, $ext=null) {
		return new static(function() use ($str, $name) {
			if(!$ext) {
				preg_match("/[^\.]+$/", $name, $matches);
				$ext = $matches[0];
			}
			Response::mimeType("application/$ext");
			Response::attachmentName("$name");
			return $str;
		});
	}

	public static function redirect($addr, $data=false, $params=false) {
		static::status(301);
		return new static(function() use ($addr) {
			header('Location: '.$addr);
		});
	}
}