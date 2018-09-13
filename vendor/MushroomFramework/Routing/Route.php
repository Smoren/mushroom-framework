<?php

namespace MushroomFramework\Routing;

// класс маршрута

/**
 * Class Route
 * @package MushroomFramework\Routing
 */
class Route {
	protected static $patterns; // понадобится в дальнейшем (как в Laravel)
	protected $mask; // маска URI
	protected $addr; // Controller.action
	protected $regexp; // маска URI, преобразованная в regexp
	protected $controller; // имя контроллера
	protected $action; // имя действия
	protected $data; // ассоциативный массив аргументов для action
	protected $decoratorClassName; // имя класса-декоратора
	protected $decoratorParams; // имя декорирования
	protected $args = array(); // имена аргументов для action

	// фабрика для создания и добавления маршрута в диспетчер
	public static function register($mask, $addr) {
		$route = new static($mask, $addr);
		return Router::addRoute($route);
	}

	// фабрика для создания и добавления маршрута в диспетчер
	public static function rest($mask, $controllerName, $idRegExp='[0-9]+') {
		$mask = preg_replace('/[\/]+$/', '', $mask);
		
		// добавляем маршрут для доступа к collection
		$route = new static($mask, "{$controllerName}.collection");
		$collectionRoute = Router::addRoute($route);
		
		// добавляем маршрут для доступа к item
		$route = new static("{$mask}/%id%", "{$controllerName}.item");
		$itemRoute = Router::addRoute($route);
		$itemRoute->where(array(
			'id' => $idRegExp,
		));
		return new RouteCollection(array(
			'collection' => $collectionRoute,
			'item' => $itemRoute,
		));
	}

	// фабрика создания и добавления обработчика ошибок
	public static function error($exceptionName, $addr) {
		$route = new static(false, $addr);
		return Router::addError($exceptionName, $route);
	}

	function __construct($mask, $addr) {
		$this->setMask($mask);
		$this->setAddr($addr);
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

	// устанавливает Controller.action
	public function setAddr($addr) {
		$arAddr = explode('.', $addr);
		$this->addr = $addr;
		$this->controller = $arAddr[0];
		$this->action = $arAddr[1];
	}

	// запуск Controller.action
	public function go(Router $router) {
		$controllerName = $this->controller.'Controller';
		try {
			$controller = new $controllerName();
		} catch (\Exception $e) {
			throw new Exceptions\RouteException("Class {$this->controller} not exists");
		}

		if($this->decoratorClassName) {
			$decoratorClassName = $this->decoratorClassName;
			if(class_exists($decoratorClassName)) {
				$decorator = new $decoratorClassName($controller, $this->decoratorParams);
				if(!($decorator instanceof ControllerDecorator)) {
					throw new \Exception("Wrong ControllerDecorator {$decoratorClassName}");
				}
			} else {
                throw new \Exception("Wrong ControllerDecorator {$decoratorClassName} (class not exists)");
            }
		} else {
			$decorator = new ControllerDecorator($controller);
		}
		return $router->handle($decorator, $controller, $this->action, $this->data);
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
		// TODO: data в отдельный метод
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

	// устанавливает декоратор
	public function decorate($decoratorClassName, $decoratorParams=array()) {
		$this->decoratorClassName = $decoratorClassName;
		$this->decoratorParams = $decoratorParams;
		return $this;
	}
}