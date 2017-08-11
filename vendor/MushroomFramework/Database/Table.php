<?php

namespace MushroomFramework\Database;
use \Exception;

/**
 * Table Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class Table {
	/**
	 * @var string $tableName
	 */
	protected $tableName;
	
	/**
	 * Creates and returns Table object
	 * @param string $tableName
	 * @param array $fields
	 * @return Table
	 */
	public static function create($tableName, $fields) {
		QueryBuilder::createTable($tableName, $fields)->exec();
		return new static($tableName);
	}

	/**
	 * Searches and returns Table object
	 * @param string $tableName
	 * @throws Exception
	 * @return Table
	 */
	public static function get($tableName) {
		if(!QueryBuilder::showTables($tableName)->exec()->fetch()) {
			throw new Exception("table '$tableName' is not exist");
		}
		return new static($tableName);
	}

	protected function __construct($tableName) {
		$this->tableName = $tableName;
	}

	/**
	 * Drops table
	 * @return $this
	 */
	public function drop() {
		QueryBuilder::dropTable($this->tableName)->exec();
		return $this;
	}

	/**
	 * Adds column
	 * @param string $colName
	 * @param array $params
	 * @return $this
	 */
	public function addColumn($colName, $params) {
		$this->alter()->addColumn($colName, $params)->exec();
		return $this;
	}

	/**
	 * Edits column
	 * @param string $colName
	 * @param array $params
	 * @return $this
	 */
	public function editColumn($colName, $params) {
		$this->alter()->editColumn($colName, $params)->exec();
		return $this;
	}
	
	/**
	 * Drops column
	 * @param string $colName
	 * @return $this
	 */
	public function dropColumn($colName) {
		$this->alter()->dropColumn($colName)->exec();
		return $this;
	}
	
	/**
	 * Adds index
	 * @param string $colName
	 * @return $this
	 */
	public function addIndex($colName) {
		$this->alter()->addIndex($colName)->exec();
		return $this;
	}
	
	/**
	 * Drops index
	 * @param string $colName
	 * @return $this
	 */
	public function dropIndex($colName) {
		$this->alter()->dropIndex($colName)->exec();
		return $this;
	}
	
	/**
	 * Adds unique index
	 * @param string $colName
	 * @return $this
	 */
	public function addUnique($colName) {
		$this->alter()->addUnique($colName)->exec();
		return $this;
	}

	/**
	 * Adds foreign key
	 * @param string $colName
	 * @param string $forTable
	 * @param string $forColumn
	 * @param string $onDelete
	 * @param string $onUpdate
	 * @return $this
	 */
	public function addForeignKey($colName, $forTable, $forColumn, $onDelete=false, $onUpdate=false) {
		$this->alter()->addForeignKey($colName, $forTable, $forColumn, $onDelete, $onUpdate)->exec();
		return $this;
	}

	/**
	 * Starts ALTER TABLE query
	 * @return QueryBuilder
	 */
	public function alter() {
		return QueryBuilder::alterTable($this->tableName);
	}
}