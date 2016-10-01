<?php

namespace MushroomFramework\Database\Mysql;
use MushroomFramework\Database;

/**
 * Mysql QueryResult Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class QueryResult extends Database\QueryResult {
	function __construct($queryResult, $handle) {
		$this->handle = $handle;
		$this->queryResult = $queryResult;
	}

	/**
	 * Fetchs and returns row
	 * @return array
	 */
	public function fetch() {
		return @mysqli_fetch_assoc($this->queryResult);
	}

	/**
	 * Returns result resource
	 * @return resurce
	 */
	public function getResult() {
		return $this->queryResult;
	}

	/**
	 * Returns id of the last inserted row
	 * @return int
	 */
	public function getInsertedId() {
		return mysqli_insert_id($this->handle);
	}

	/**
	 * Returns error text
	 * @return string
	 */
	public function getError() {
		return mysqli_error($this->handle);
	}

	/**
	 * Returns error number
	 * @return int
	 */
	public function getErrorNo() {
		return mysqli_errno($this->handle);
	}
}
