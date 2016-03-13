<?php

namespace MushroomFramework\Database;
use \ReflectionException;
use \Exception;

/**
 * DatabaseManager Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
abstract class DatabaseManager {
	/**
	 * @var DatabaseManager $instance
	 */
	protected static $instance;

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
	 * Returns DatabaseManager object of database type ($config['database']['type'])
	 * @throws ReflectionException
	 * @param array $config Config array from /app/config/config.php
	 * @return mixed
	 */
	public static function get(array $config) {
		$className = "\\MushroomFramework\\Database\\".ucfirst(mb_strtolower($config['type']))."\\DatabaseManager";
		if(class_exists($className)) {
			return new $className($config);
		}
		throw new ReflectionException("class '$className' doesn't exist");
	}

	/**
	 * Returns DatabaseManager object of the first opened connection
	 * @return DatabaseManager
	 */
	abstract public function gi();

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
	 * Returns the encoding used by DatabaseManager
	 * @return string
	 */
	public function getEncoding() {
		return $this->encoding;
	}

	/**
	 * Returns the collate used by DatabaseManager
	 * @return string
	 */
	public function getCollate() {
		return $this->collate;
	}

	/**
	 * Closes opened connection to database
	 * @return void
	 */
	public function close() {
		mysqli_close($this->handle);
	}
    
    function __destruct() {
		$this->close();
	}
}
