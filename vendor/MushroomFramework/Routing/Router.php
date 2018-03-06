<?php

namespace MushroomFramework\Routing;

// принимает запросы, собирает данные и осуществляет маршрутизацию
class Router {
	protected static $instance;
	protected static $routes = array();
	protected static $routesTree = array();
	protected static $errors = array();
	protected $method; // метод (get/post)
	protected $uri; // строка запроса
	protected $arUri = array(); // распарсированный запрос
	protected $data = array(); // данные, которые будут переданы аргументами в action

	public static function gi() {
		return static::$instance;
	}

	public static function getRequestMethod() {
		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	// добавляет маршрут
	public static function addRoute(Route $route) {
		static::$routes[] = $route;
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
	public static function findRoute($addr, $argsSize) {
		if(isset(static::$routesTree[$addr][$argsSize])) {
			return static::$routesTree[$addr][$argsSize];
		} else {
			throw new Exceptions\RouteException("route '$addr' with size of args $argsSize not found");
		}
	}

	function __construct($uri=false, $method=false) {
		// парсим URI
		if(!$uri) $uri = $_SERVER['REQUEST_URI'];
		$this->parseUri($uri);

		// устанавливаем метод
		if(!$method) $method = static::getRequestMethod();
		$this->setMethod($method);

		if(!static::$instance) static::$instance = $this;
	}

	// возвращает маршрут обработки ошибки по ее имени (Exception)
	public function getError($code=404) {
		if(isset(static::$errors[$code])) {
			return static::$errors[$code];
		}
		return false;
	}

	// возвращает метод (get/post)
	public function getMethod() {
		return $this->method;
	}

	// устанавливает метод (get/post/...)
	public function setMethod($method) {
		$this->method = strtoupper($method);
	}

	// сравнивает текущий метод с переданным
	public function checkMethod($method) {
		return strtoupper($this->method) === strtoupper($method);
	}

	// принимает решение о маршрутизации, 
	public function dispatch() {
		// пробуем найти подходящий маршрут
		foreach(static::$routes as $route) {
			if($route->check($this->uri)) {
				return $route->go($this);
			}
		}
		return $this->handle();
	}

	// обрабатывает ошибки перед выполнением или передачей управления
	public function handle($decorator=false, $controller=false, $actionName='', $data=array()) {
		try {
			if(!$controller) {
				throw new Exceptions\StatusException(404, "no route found");
			} elseif(!($controller instanceof Controller)) {
				throw new Exceptions\RouteException("Controller object is not instance of Controller");
			}

			if(!$decorator) {
				$decorator = new ControllerDecorator($controller);
			} elseif(!($decorator instanceof ControllerDecorator)) {
				throw new Exceptions\RouteException("Controller decorator object is not instance of ControllerDecorator");
			}

			$response = call_user_func_array(array($decorator, $actionName), $data);
			if(!$response) {
				throw new Exceptions\StatusException(404, 'no response found');
			}

			return $response;
		} catch(Exceptions\StatusException $e) {
			if($route = static::getError($e->getStatus())) {
				$router = $this;
				return $route->go($router);
			}
			$statusCode = 500;
			http_response_code($statusCode);
			throw new Exceptions\StatusException($statusCode, 'no handler for '.$e->getStatus());
		}
	}
	
	// передача управления
	public function transfer($where, $data=array(), $decorator=null) {
		// TODO переделать через поиск и запуск Route::go
		foreach(static::$routes as &$route) {
			if($route->getAddr() == $where) {
				return $route->go($this);
			}
		}
		foreach(static::$errors as &$route) {
			if($route->getAddr() == $where) {
				return $route->go($this);
			}
		}
		throw new Exceptions\RouteException("Transfer error for {$where}");
	}

	public static function throwStatusException($code, $message) {
		throw new Exceptions\StatusException($code, $message);
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