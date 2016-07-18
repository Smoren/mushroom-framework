<?php

namespace MushroomFramework\Routing;

// принимает запросы, собирает данные и осуществляет маршрутизацию
class Router {
	protected static $instance;
	protected static $routes = array();
	protected static $routesTree = array();
	protected static $errors = array();
	protected $controller; // имя контроллера
	protected $action; // имя дейтсвия
	protected $method; // метод (get/post)
	protected $uri; // строка запроса
	protected $arUri = array(); // распарсированный запрос
	protected $data = array(); // данные, которые будут переданы аргументами в action
	protected $controllerInstance; // экземпляр используемого контроллера

	function __construct($uri=false, $method=false) {
		// парсим URI
		if(!$uri) $uri = $_SERVER['REQUEST_URI'];
		$this->parseUri($uri);

		// устанавливаем метод
		if(!$method) $method = static::getRequestMethod();
		$this->setMethod($method);
	}

	public static function getRequestMethod() {
		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	// добавляет маршрут
	public static function addRoute(Route $route) {
		static::$routes[] = $route;
		$method = $route->getMethod();
		$addr = $route->getAddr();
		$argsSize = $route->getArgsSize();
		$route->addToTree(static::$routesTree);
		return $route;
	}

	// добавляет обработчик ошибок
	public static function addError($exceptionName, Route $route) {
		static::$errors[$exceptionName] = $route;
		return $route;
	}

	// возвращает массив маршрутов
	public static function getRoutes() {
		return static::$routes;
	}

	// ищет маршрут
	public static function findRoute($addr, $argsSize, $method=false) {
		if(!$method) {
			$method = $this->getCurrentMethod();
		}
		if(isset(static::$routesTree[$method][$addr][$argsSize])) {
			return static::$routesTree[$method][$addr][$argsSize];
		} else {
			throw new Exceptions\RouteException("route '$addr' with size of args $argsSize not found");
		}
	}

	// ищет маршрут с любым методом
	public static function findAnyRoute($addr, $argsSize) {
		$method = static::getRequestMethod();
		$route = null;
		foreach(static::$routesTree as $method => $val) {
			try {
				$route = static::findRoute($addr, $argsSize, $method);
				if($route) break;
			} catch(Exceptions\RouteException $e) {
				$errorMessage = $e->getMessage();
			}
		}
		if(!$route) {
			throw new Exceptions\RouteException($errorMessage);
		}
		return $route;
	}

	// возвращает маршрут обработки ошибки по ее имени (Exception)
	public function getError($name=false) {
		if($name && isset(static::$errors[$name])) {
			return static::$errors[$name];
		}
		return false;
	}

	// возвращает метод (get/post)
	public function getMethod() {
		return $this->method;
	}

	// устанавливает метод (get/post)
	public function setMethod($method) {
		$this->method = strtolower($method);
	}

	// устанавливает действие
	public function setAction($action) {
		$this->action = $this->method.\ucfirst($action);
	}

	// устанавливает контроллер
	public function setController($controller) {
		$this->controller = "\\".\ucfirst($controller).'Controller';
	}

	// устанавливает аргумент для передачи в action
	public function setData(Array $data) {
		$this->data = $data;
	}

	// возвращает данные
	public function getData() {
		return $this->data;
	}

	// принимает решение о маршрутизации, 
	public function dispatch() {
		// пробуем найти подходящий маршрут
		foreach(static::$routes as $route) {
			if($route->check($this->method, $this->uri)) {
				return $route->go($this);
			}
		}
		return $this->handle();
	}

	// обрабатывает ошибки перед выполнением или передачей управления
	public function handle() {
		try {
			return $this->transfer();
		} catch(Exceptions\PageNotFoundException $e) {
			if($route = static::getError('PageNotFound')) {
				$router = $this;
				return $route->go($router);
			}
			throw new Exceptions\PageNotFoundException('no 404 handler');
		}
	}

	// передает управление контроллеру
	public function transfer() {
		if(!$this->controller) {
			throw new Exceptions\PageNotFoundException("No route found");
		} elseif(!class_exists($this->controller)) {
			throw new Exceptions\RouteException("Class {$this->controller} not exists");
		} elseif(!method_exists($this->controller, $this->action)) {
			throw new Exceptions\RouteException("Method {$this->controller}::{$this->action}() not exists");
		} else {
			$this->controllerInstance = new $this->controller();
			return call_user_func_array(array($this->controllerInstance, $this->action), $this->data);
		}
	}
	
	// парсит URI
	protected function parseUri($uri) {
		$arUri = explode('?', $uri);
		$this->uri = urldecode($arUri[0]);
		if($this->uri[strlen($this->uri)-1] !== '/') {
			$this->uri .= '/';
		}
		$arUri = explode('/', $this->uri);
		foreach($arUri as $val) {
			if($val !== '') $this->arUri[] = $val;
		}
	}
}