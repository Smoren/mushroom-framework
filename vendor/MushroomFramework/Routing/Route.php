<?php

namespace MushroomFramework\Routing;

// класс маршрута
class Route {
	protected static $patterns; // понадобится в дальнейшем (как в Laravel)
	protected $mask; // маска URI
	protected $addr; // Controller.action
	protected $regexp; // маска URI, преобразованная в regexp
	protected $callback; // функция обратного вызова
	protected $controller; // имя контроллера
	protected $action; // имя действия
	protected $data; // ассоциативный массив аргументов для action/callback
	protected $args = array(); // имена аргументов для action/callback

	// фабрика для создания и добавления маршрута в диспетчер (method=any)
	public static function register($mask, $callback) {
		$route = new static($mask, $callback);
		return Router::addRoute($route);
	}

	// фабрика создания и добавления обработчика ошибок
	public static function error($exceptionName, $callback) {
		$route = new static(false, $callback);
		return Router::addError($exceptionName, $route);
	}

	function __construct($mask, $callback) {
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

	// устанавливает callback/Controller.action
	public function setCallback($callback) {
		if($callback instanceof \Closure) {
			$this->callback = $callback;
		} else {
			$arCallback = explode('.', $callback);
			$this->addr = $callback;
			$this->controller = $arCallback[0];
			$this->action = $arCallback[1];
		}
	}

	// запуск callback/Controller.action
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

	// возвращает контроллер
	public function getController() {
		return $this->controller;
	}

	// возвращает действие
	public function getAction() {
		return $this->action;
	}

	// возвращает Controller.action
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
		$argsSize = sizeof($this->args);
		if(isset($tree[$this->addr][$argsSize])) {
			throw new \Exception("routes collision found on {$this->addr} (size of arguments: $argsSize)");
		}
		$tree[$this->addr][$argsSize] = $this;
		return true;
	}

	// проверяет, подходит ли переданный URI под маску маршрута, собирает аргументы для callback/Controller.action
	public function check($uri) {
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