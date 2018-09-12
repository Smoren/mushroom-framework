<?php

namespace MushroomFramework\ORMushroom\Mysql;
use MushroomFramework\ORMushroom\Base;
use MushroomFramework\ORMushroom\Exceptions\QueryException;
use \Exception;

/**
 * Mysql DatabaseManager Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class DatabaseSession extends Base\DatabaseSession {
	public function __construct($dbConfig) {
		$this->type = $dbConfig['type'];
		$this->host = $dbConfig['host'];
		$this->username = $dbConfig['username'];
		$this->password = $dbConfig['password'];
		$this->dbName = $dbConfig['dbName'];

		if(isset($dbConfig['encoding']) && $dbConfig['encoding']) {
			$this->encoding = $dbConfig['encoding'];
		}

		if(isset($dbConfig['collate']) && $dbConfig['collate']) {
			$this->collate = $dbConfig['collate'];
		}

		if(!function_exists('mysqli_connect')) {
			throw new Exception("mysqli library required");
		}

		// создаем соединение
		if(!($this->handle = @mysqli_connect($this->host, $this->username, $this->password))) {
			throw new Exception("can't connect to mysql using host: '{$this->host}', username: '{$this->username}', password: '{$this->password}'");
		}

		// выбираем БД
		if(!mysqli_select_db($this->handle, $this->dbName)) {
			throw new Exception("can't select database: '{$this->dbName}'");
		}

		// выставляем кодировку
		$this->query("SET NAMES '{$this->encoding}'");
		$this->query("SET CHARACTER SET {$this->encoding}");
		$this->query("SET COLLATION_CONNECTION = '{$this->collate}'");
	}

	/**
	 * Returns QueryResult object as a result of the query from $queryString
	 * @param string $queryString Query string
	 * @return QueryResult
	 */
	public function query($queryString) {
		$rs = mysqli_query($this->handle, $queryString);
		if(!$rs) throw new QueryException(mysqli_error($this->handle));
		return new QueryResult($rs, $this->handle);
	}

	/**
	 * Checks if $tableName exists
	 * @param string $tableName Name of the table
	 * @return boolean
	 */
	public function tableExists($tableName) {
		$tableName = addslashes($tableName);
		if($this->query("SHOW TABLES LIKE '$tableName'")->fetch()) return true;
		return false;
	}

	/**
	 * Starts new transaction
	 */
	public function transactionStart() {
		$this->query("set autocommit=0");
		$this->query("start transaction");
	}

	/**
	 * Commits transaction
	 */
	public function transactionCommit() {
		$this->query("commit");
		$this->query("set autocommit=1");
	}

	/**
	 * Rollbacks transaction
	 */
	public function transactionRollback() {
		$this->query("rollback");
		$this->query("set autocommit=1");
	}

	public function close() {
		mysqli_close($this->handle);
		parent::close();
	}
}
