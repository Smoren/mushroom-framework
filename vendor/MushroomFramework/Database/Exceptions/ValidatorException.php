<?php

namespace MushroomFramework\Database\Exceptions;

class ValidatorException extends \Exception {
	protected $errorFields;

	function __construct(array $errorFields, $message='') {
		$this->errorFields = $errorFields;
		parent::__construct($message);
	}

	public function getErrorFields() {
		return $this->errorFields;
	}
}
