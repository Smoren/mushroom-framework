<?php

class AccessManager extends ControllerDecorator {
	protected function _onActionCall(&$methodName, &$args) {
		// Router::throwStatusException(404, 'test');
	}

	protected function _onActionFound(&$methodName, &$args) {
		
	}

	protected function _onActionResponse(&$response) {
		
	}
}