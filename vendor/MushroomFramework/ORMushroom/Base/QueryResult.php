<?php

namespace MushroomFramework\ORMushroom\Base;

/**
 * QueryResult Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
abstract class QueryResult {
	/**
	 * @var resource $handle Database connection handler
	 */
	protected $handle;

	/**
	 * @var resource $queryResult Database query result
	 */
	protected $queryResult;
	
	function __construct($queryResult, $handle) {
		$this->handle = $handle;
		$this->queryResult = $queryResult;
	}

	/**
	 * Fetchs and returns row
	 * @return array
	 */
	abstract public function fetch();

	/**
	 * Returns result resource
	 * @return resurce
	 */
	abstract public function getResult();

	/**
	 * Returns id of the last inserted row
	 * @return int
	 */
	abstract public function getInsertedId();

	/**
	 * Returns error text
	 * @return string
	 */
	abstract public function getError();

	/**
	 * Returns error number
	 * @return int
	 */
	abstract public function getErrorNo();

	/**
	 * Returns array of rows
	 * @return array
	 */
	public function getList() {
		$result = array();
		while($row = $this->fetch()) {
			$result[] = $row;
		}
		return $result;
	}

	/**
	 * Returns array of rows
	 * @return array
	 */
	public function list() {
		return $this->getList();
	}
}
