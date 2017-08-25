<?php

namespace MushroomFramework\Routing;

class ControllerDecorator {
	protected $controller;
	protected $params;

	function __construct(Controller $controller, $params=array()) {
		$this->controller = $controller;
		$this->params = $params;
	}

	public function __call($methodName, $args=array()) {
		if(!$this->controller) {
			throw new Exceptions\RouteException("ControllerDecorator ".get_called_class()." has no Controller to decorate");
		}

		if($r = $this->_onActionCall($methodName, $args)) {
			return $r;
		}
		
		if(is_callable(array($this->controller, $methodName))) {
			if($r = $this->_onActionFound($methodName, $args)) {
				return $r;
			}
			$response = call_user_func_array(array($this->controller, $methodName), $args);
			if($r = $this->_onActionResponse($response)) {
				return $r;
			}
			return $response;
		} else {
			throw new Exceptions\RouteException(get_class($this->controller).'::'.$methodName.' is not callable');
		}
	}

	public function getController() {
		return $this->controller;
	}

	protected function _onActionCall(&$methodName, &$args) {
		
	}

	protected function _onActionFound(&$methodName, &$args) {
		
	}

	protected function _onActionResponse(&$response) {
		
	}
}