<?php

namespace MushroomFramework\ORMushroom\Base;
use \ReflectionException;
use \Exception;

/**
 * DatabaseManager Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
abstract class DatabaseSession {
	/**
	 * @var string $type database type
	 */
	protected $type;

	/**
	 * @var string $host Hostname
	 */
	protected $host;

	/**
	 * @var string $username Username
	 */
	protected $username;

	/**
	 * @var string $password Password
	 */
	protected $password;

	/**
	 * @var string $dbName Database name
	 */
	protected $dbName;

	/**
	 * @var string $encoding Encoding
	 */
	protected $encoding = 'utf8';

	/**
	 * @var string $collate Collate
	 */
	protected $collate = 'utf8_general_ci';

	/**
	 * @var resource $handle Database connection handle
	 */
	protected $handle; // здесь хранится идентификатор соединения

	/**
	 * Returns QueryResult object as a result of the query from $queryString
	 * @param string $queryString Query string
	 * @return QueryResult
	 */
	abstract public function query($queryString);

	/**
	 * Checks if $tableName exists
	 * @param string $tableName Name of the table
	 * @return boolean
	 */
	abstract public function tableExists($tableName);

	/**
	 * Starts new transaction
	 */
	abstract public function transactionStart();

	/**
	 * Commits transaction
	 */
	abstract public function transactionCommit();

	/**
	 * Rollbacks transaction
	 */
	abstract public function transactionRollback();

	/**
	 * Closes opened connection to database
	 * @return void
	 */
	public function close() {
		$this->handle = null;
	}

	public function __get($key) {
		if(isset($this->$key)) {
			return $this->key;
		} else {
			throw new ReflectionException("DatabaseManager::$key is not defined");
		}
	}

    function __destruct() {
		if($this->handle) $this->close();
	}
}
