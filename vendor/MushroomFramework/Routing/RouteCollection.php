<?php

namespace MushroomFramework\Routing;

// класс коллекции маршрута
class RouteCollection {
	protected $collection = array();

	function __construct($list) {
		foreach($list as &$route) {
			if(!($route instanceof Route)) {
				throw new \Exception('Object in RouteCollection is not a route');
			}
			$this->collection[] = $route;
		}
	}
	
	public function __call($methodName, $args=array()) {
		foreach($this->collection as &$route) {
			if(is_callable(array($route, $methodName))) {
				call_user_func_array(array($route, $methodName), $args);
			} else {
				throw new \Exception(get_class($route).'::'.$methodName.' is not callable');
			}
		}
		return $this;
	}
}