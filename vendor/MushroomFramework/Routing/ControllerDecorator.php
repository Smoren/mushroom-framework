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

		$this->_onActionCall($methodName, $args);
		
		if(is_callable(array($this->controller, $methodName))) {
			$this->_onActionFound($methodName, $args);
			$response = call_user_func_array(array($this->controller, $methodName), $args);
			$this->_onActionResponse($response);
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