<?php

namespace MushroomFramework\InputOutput;
use \Exception;

class Request {
	public static function getMethod() {
		return mb_strtolower($_SERVER['REQUEST_METHOD']);
	}

	public static function isAjax() {
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		 	return true;
		}
		return false;
	}

	public static function input($key=null, $default=null) {
		if(!$key) return $_REQUEST;
		if(!isset($_REQUEST[$key]) && $default === null) {
			throw new Exception("\$_REQUEST['$key'] is not defined");
		}
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
	}

	public static function only(...$keys) {
		$res = array();
		foreach($keys as $key) {
			if(!isset($_REQUEST[$key])) {
				$res[$key] = null;
			} else {
				$res[$key] = $_REQUEST[$key];
			}
		}
		return $res;
	}

	// TODO: протестить
	public static function except(...$data) {
		$res = array();
		foreach($_REQUEST[$key] as $key => $val) {
			if(in_array($key, $data)) continue;
			$res[$key] = $val;
		}
		return $res;
	}

	public static function get($key=null, $default=null) {
		if(!$key) return $_GET;
		if(!isset($_GET[$key]) && $default === null) {
			throw new Exception("\$_GET['$key'] is not defined");
		}
		return isset($_GET[$key]) ? $_GET[$key] : $default;
	}

	public static function post($key=null, $default=null) {
		if(!$key) return $_POST;
		if(!isset($_POST[$key]) && $default === null) {
			throw new Exception("\$_POST['$key'] is not defined");
		}
		return isset($_POST[$key]) ? $_POST[$key] : $default;
	}

	public static function cookie($key=null, $default=null) {
		if(!$key) return $_COOKIE;
		if(!isset($_COOKIE[$key]) && $default === null) {
			throw new Exception("\$_COOKIE['$key'] is not defined");
		}
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
	}

	public static function json($key=null, $default=null) {
		$postData = json_decode(file_get_contents('php://input'), true);
		if(!$key) return $postData;
		if(!isset($postData[$key]) && $default === null) {
			throw new Exception("'$key' is not defined");
		}
		return isset($postData[$key]) ? $postData[$key] : $default;
	}

	public static function all() {
		return $_REQUEST;
	}
}
