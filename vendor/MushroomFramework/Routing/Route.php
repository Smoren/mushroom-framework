<?php

namespace MushroomFramework\Routing;

// класс маршрута
class Route {
	protected static $patterns; // понадобится в дальнейшем (как в Laravel)
	protected static $methods = array('get', 'post'); // все методы
	protected $method; // метод (any=FALSE/get/post)
	protected $mask; // маска URI
	protected $regexp; // маска URI, преобразованная в regexp
	protected $callback; // функция обратного вызова
	protected $addr; // Controller@action
	protected $controller; // имя контроллера
	protected $action; // имя действия
	protected $data; // ассоциативный массив аргументов для action/callback
	protected $args = array(); // имена аргументов для action/callback

	function __construct($method, $mask, $callback) {
		$this->method = $method;
		$this->setMask($mask);
		$this->setCallback($callback);
		$this->data = array();
	}

	// парсит и устанавливает маску
	public function setMask($mask) {
		$mask = str_replace(array('{', '}'), '%', $mask);
		$this->mask = $mask;
		preg_match_all('/%([A-Za-z0-9_\-]+)%/', $mask, $matches);
		foreach($matches[1] as $match) {
			$this->args[] = $match;
		}
		$this->regexp = '/^'.preg_quote($mask, '/').'[\/]{0,1}$/';
	}

	// устанавливает callback/Controller@action
	public function setCallback($callback) {
		if($callback instanceof \Closure) {
			$this->callback = $callback;
		} else {
			$this->addr = $callback;
			$callback = explode('@', $callback);
			$this->controller = $callback[0];
			$this->action = $callback[1];
		}
	}

	// запуск callback/Controller@action
	public function go(Router $router) {
		$router->setData($this->data);
		if($this->callback instanceof \Closure) {
			return call_user_func_array($this->callback, $this->data);
		} else {
			$router->setController($this->controller);
			$router->setAction($this->action);
			$router->setData($this->data);
			return $router->handle();
		}
	}

	// фабрика для создания и добавления маршрута в диспетчер (method=any)
	public static function any($mask, $callback) {
		$route = new static(false, $mask, $callback);
		return Router::addRoute($route);
	}

	// фабрика для создания и добавления маршрута в диспетчер (method=get)
	public static function get($mask, $callback) {
		$route = new static('get', $mask, $callback);
		return Router::addRoute($route);
	}

	// фабрика для создания и добавления маршрута в диспетчер (method=post)
	public static function post($mask, $callback) {
		$route = new static('post', $mask, $callback);
		return Router::addRoute($route);
	}

	// фабрика создания и добавления обработчика ошибок
	public static function error($exceptionName, $callback) {
		$route = new static(false, false, $callback);
		return Router::addError($exceptionName, $route);
	}

	// передача управления
	public static function transfer(Router $router, $where, $data=false) {
		$route = new static(false, false, $where);
		$router->setController($route->getController());
		$router->setAction($route->getAction());
		if(is_array($data)) {
			$router->setData($data);
		}
		return $router->handle();
	}

	// возвращает метод (any=FALSE/get/post)
	public function getMethod() {
		return $this->method;
	}

	// возвращает контроллер
	public function getController() {
		return $this->controller;
	}

	// возвращает действие
	public function getAction() {
		return $this->action;
	}

	// возвращает Controller@action
	public function getAddr() {
		return $this->addr;
	}

	// возвращает количество аргументов
	public function getArgsSize() {
		return sizeof($this->args);
	}

	// возвращает URI
	public function getUri(Array $data) {
		$i=0;
		$res = $this->mask;
		foreach($data as $val) {
			$res = str_replace("%{$this->args[$i]}%", $val, $res);
			$i++;
		}
		return $res;
	}

	// добавляет маршрут в дерево
	public function addToTree(Array &$tree) {
		if(!$this->addr) return false;
		if(!$this->method) {
			$methods = static::$methods;
		} else {
			$methods = array($this->method);
		}
		$argsSize = sizeof($this->args);
		foreach($methods as $method) {
			if(isset($tree[$method][$this->addr][$argsSize])) {
				continue;
				// throw new \Exception("routes collision found on {$this->addr} (method: $method, size of arguments: $argsSize)");
			}
			$tree[$method][$this->addr][$argsSize] = $this;
		}
		return true;
	}

	// проверяет, подходит ли переданный URI под маску маршрута, собирает аргументы для callback/Controller@action
	public function check($method, $uri) {
		if($this->method && $method != $this->method) return false;
		preg_match($this->regexp, $uri, $matches);
		if(!sizeof($matches)) return false;
		for($i=1; $i<sizeof($matches); $i++) {
			$this->data[$this->args[$i-1]] = $matches[$i];
		}
		return true;
	}

	// устанавливает условия валидации и парсинга маски
	public function where(Array $arReplace) {
		$mask = $this->regexp;
		foreach($arReplace as $key => $val) {
			$mask = str_replace("%$key%", '('.$val.')', $mask);
		}
		$this->regexp = $mask;
		return $this;
	}
}