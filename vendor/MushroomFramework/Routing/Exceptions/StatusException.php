<?php

namespace MushroomFramework\Routing\Exceptions;

class StatusException extends RouteException {
	protected $status;

	function __construct($status, $message='') {
		parent::__construct($message);
		$this->status = $status;
	}

	public function getStatus() {
		return $this->status;
	}
}