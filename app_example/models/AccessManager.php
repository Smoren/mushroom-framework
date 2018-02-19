<?php

class AccessManager extends ControllerDecorator {
	protected function _onActionCall(&$methodName, &$args) {
		// return Router::transfer('Index.index');
		// Router::throwStatusException(404, 'test');
	}

	protected function _onActionFound(&$methodName, &$args) {
		// return Response::text('test');
	}

	protected function _onActionResponse(&$response) {
		
	}
}