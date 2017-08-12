<?php

namespace MushroomFramework\Database;

/**
 * Validator Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class Validator {
	/**
	 * @var array $fields Array of fields
	 */
	protected $fields;

	/**
	 * @var array $rules Array of rules
	 */
	protected $rules;

	/**
	 * @var array $errorFields Array of error fields
	 */
	protected $errorFields = array();

	function __construct($fields, $rules) {
		$this->fields = $fields;
		$this->rules = $rules;
	}

	public function __get($key) {
		if(!isset($this->$key)) return false;
		return $this->$key;
	}

	/**
	 * Sets encoding and collate
	 * @param array $data
	 * @return $this
	 */
	public function validate($data) {
		$this->reset();

		// проверим required
		foreach($this->rules as $fieldName => $fieldRules) {
			if($fieldRules['required']) {
				if(!isset($data[$fieldName]) || !strlen($data[$fieldName])) {
					$this->errorFields[$fieldName] = 0;
				}
			}
		}

		// теперь проверим правило
		foreach($data as $fieldName => $value) {
			if(!in_array($fieldName, $this->fields)) continue;
			if(!isset($this->rules[$fieldName]['rule']) || !$this->rules[$fieldName]['rule']) continue;
			$rule = $this->rules[$fieldName]['rule'];
			if(is_callable($rule)) {
				$isValid = $rule($value, $this);
			} else {
				$isValid = preg_match($rule, $value);
			}
			if(!$isValid && !isset($this->errorFields[$fieldName])) $this->errorFields[$fieldName] = 1;
		}
		return $this;
	}

	/**
	 * Resets validator state
	 * @return $this
	 */
	public function reset() {
		$this->errorFields = array();
		return $this;
	}

	/**
	 * Returns true if validation is successful
	 * @return boolean
	 */
	public function success() {
		return !boolval(sizeof($this->errorFields));
	}

	/**
	 * Returns true if validation is not successful
	 * @return boolean
	 */
	public function error() {
		return !$this->success();
	}

	/**
	 * Returns error fields' array
	 * @return Array
	 */
	public function getErrorFields() {
		return $this->errorFields;
	}
}

