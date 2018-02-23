<?php

namespace MushroomFramework\ORMushroom\Base;

use \MushroomFramework\ORMushroom\Exceptions\QueryBuilderException;
use \MushroomFramework\ORMushroom\DatabaseSession;
use \ReflectionException;
use \ReflectionMethod;

/**
 * QueryBuilder Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class QueryBuilder {
	protected static $_operators = array('and', 'or', 'left', 'right', 'inner', 'outer');
	protected static $_encoding = 'utf8';
	protected static $_collate = 'utf8_general_ci';
	protected $_queryString;
	protected $_bracketCount;

	public static function setEncoding($encoding, $collate) {
		static::$_encoding = $encoding;
		static::$_collate = $collate;
		
		return $this;
	}

	public static function __callStatic($methodName, $args) {
		// получаем имя класса
		$className = get_called_class();
		
		// если метод существует
		if(method_exists($className, $methodName)) {
			// получаем объект метода
			$method = new ReflectionMethod($className, $methodName);

			// не позволим вызвать метод, если он является приватным либо начинается с символа "_"
			if(preg_match('/^_/', $methodName) || $method->isPrivate()) {
				throw new ReflectionException("calling forbidden internal method '$methodName'");
			}

			// создаем объект
			$qb = new static();

			// запускаем метод и возвращаем его результат
			return $qb->$methodName(...$args);
		}

		throw new ReflectionException("unknown static method '$methodName' called");
	}

	public function __call($methodName, $args) {
		// если метод класса определен
		if(method_exists($this, $methodName)) {
			$method = new ReflectionMethod($this, $methodName);

			// не позволим вызвать метод, если он является приватным либо начинается с символа "_"
			if(preg_match('/^_/', $methodName) || $method->isPrivate()) {
				throw new ReflectionException("forbidden internal method '$methodName' called");
			}

			// запускаем метод и возвращаем его результат
			return $this->$methodName(...$args);
		}

		if(in_array($methodName, static::$_operators)) {
			return $this->raw(strtoupper($methodName));
		}

		// пробуем найти метод с оператором (например, andWhere)
		if(preg_match('/^([a-z]+)([A-Z][A-Za-z]+)$/', $methodName, $matches)) {
			$operator = $matches[1];
			$methodName = $matches[2];
			if(!in_array($operator, static::$_operators)) {
				throw new QueryBuilderException("wrong operator '$operator'");
			} elseif(!method_exists($this, $methodName)) {
				throw new QueryBuilderException("unknown method '$methodName' called");
			} else {
				$this->continueBracket();
				$this->raw(strtoupper($operator));
				array_push($args, true);
				return $this->$methodName(...$args);
			}
		}

		throw new ReflectionException("unknown method '$methodName' called");
	}

	public function __construct() {
		$this->_queryString = '';
		$this->_bracketCount = 0;
	}

	public function __toString() {
		return preg_replace('/[ ]+/', ' ', $this->_queryString);
	}

	protected function exec(DatabaseSession $dbSession) {
		if($this->_bracketCount != 0) {
			throw new QueryBuilderException('uncapped bracket', $this);
		}
		return $dbSession->query($this);
	}

	protected function raw($str) {
		$this->_queryString .= " $str ";
		return $this;
	}

	protected function addQuery(QueryBuilder $qb) {
		$this->raw($qb);
	}

	protected function createDatabase($name) {
		$this->raw("CREATE DATABASE");
		$this->raw($this->shieldColumn($name));
		
		$this->raw("DEFAULT CHARACTER SET");
		$this->raw($this->shieldColumn(static::$_encoding));
		
		$this->raw("DEFAULT COLLATE");
		$this->raw($this->shieldColumn(static::$collate));

		return $this;
	}

	protected function dropDatabase($name) {
		$this->raw("DROP DATABASE");
		$this->raw($this->shieldColumn($name));
		
		return $this;
	}

	protected function showDatabases() {
		$this->raw("SHOW DATABASES");
		
		return $this;
	}

	protected function useDatabase($name) {
		$this->raw("USE");
		$this->raw($this->shieldColumn($name));
		
		return $this;
	}

	protected function showTables() {
		$this->raw("SHOW TABLES");
		
		return $this;
	}

	protected function createTable($name, array $fields) {
		$this->raw("CREATE TABLE IF NOT EXISTS");
		$this->raw($this->shieldColumn($name));
		
		$this->openBracket();
		foreach($fields as $columnName => &$params) {
			$params = $this->shieldColumn($columnName)." ".$this->shield($params);
		}
		$this->raw(implode(', ', $fields));
		$this->closeBracket();

		$this->raw("CHARACTER SET");
		$this->raw($this->shieldValue(static::$_encoding));
		$this->raw("COLLATE");
		$this->raw($this->shieldValue(static::$_collate));
		$this->raw(";");

		return $this;
	}

	protected function dropTable($name) {
		$this->raw("DROP TABLE");
		$this->raw($this->shieldColumn($name));
		
		return $this;
	}

	protected function alterTable($name) {
		$this->raw("ALTER TABLE");
		$this->raw($this->shieldColumn($name));
		
		return $this;
	}

	protected function truncate($tableName) {
		$this->raw("TRUNCATE TABLE");
		$this->raw($this->shieldColumn($name));
		
		return $this;
	}

	protected function select(...$fields) {
		$this->raw("SELECT");
		if(!sizeof($fields)) $fields = array('*');
		foreach($fields as &$value) {
			$value = $this->shieldColumn($value, true);
		}
		$this->raw(implode(', ', $fields));
		
		return $this;
	}

	protected function insert($tableName, array $fields) {
		$this->raw("INSERT INTO");
		$this->raw($this->shieldColumn($tableName));
		
		$columnNames = array();
		$values = array();
		foreach($fields as $columnName => $value) {
			$columnNames[] = $this->shieldColumn($columnName);
			$values[] = $this->shieldValue($value);
		}

		$this->openBracket();
		$this->raw(implode(', ', $columnNames));
		$this->closeBracket();

		$this->raw("VALUES");
		$this->openBracket();
		$this->raw(implode(', ', $values));
		$this->closeBracket();

		return $this;
	}

	protected function update($tableName, array $fields) {
		foreach($fields as $columnName => &$value) {
			$value = $this->shieldColumn($columnName)." = ".$this->shieldValue($value);
		}
		$this->raw("UPDATE");
		$this->raw($this->shieldColumn($tableName));
		$this->raw("SET");
		$this->raw(implode(', ', $fields));

		return $this;
	}

	protected function delete($tableName) {
		$this->raw("DELETE FROM");
		$this->raw($this->shieldColumn($tableName));
		
		return $this;
	}

	protected function from($tableName, $as=null) {
		$this->raw("FROM");
		$this->raw($this->shieldColumn($tableName));
		if($as) {
			$this->raw($this->shield($as));
		}
		
		return $this;
	}

	protected function where($key=null, $operator=null, $value=null, $continueFlag=false) {
		if(!$key) {
			$this->raw("WHERE");
			return $this;
		}

		if(!$continueFlag) {
			$this->raw("WHERE");
			$this->openBracket();
		}
		$this->condition($key, $operator, $value);
		$this->closeBracket();
		
		return $this;
	}

	protected function join($tableName, $as=null) {
		$this->raw("JOIN");
		$this->raw($this->shieldColumn($tableName));
		if($as && !intval($as)) $this->raw($this->shield($as));
		
		return $this;
	}

	protected function on($key=null, $operator=null, $value=null, $continueFlag=false) {
		if(!$key) {
			$this->raw("ON");
			return $this;
		}

		if(!$continueFlag) {
			$this->raw("ON");
			$this->openBracket();
		}
		$this->condition($key, $operator, $value);
		$this->closeBracket();
		
		return $this;
	}

	protected function order(array $by) {
		foreach($by as $columnName => &$direction) {
			$direction = $this->shieldColumn($columnName)." ".$this->shield($direction);
		}
		$this->raw("ORDER BY");
		$this->raw(implode(', ', $by));
		return $this;
	}

	protected function orderBy(array $by) {
		return $this->order($by);
	}

	protected function limit($num1, $num2=false) {
		$this->raw("LIMIT ".intval($num1));
		if(!($num2 === false)) {
			$this->raw(", ".intval($num2));
		}
		
		return $this;
	}

	protected function addColumn($columnName, $params) {
		$this->raw("ADD COLUMN");
		$this->raw($this->shieldColumn($columnName));
		$this->raw($this->shield($params));
		
		return $this;
	}

	protected function editColumn($columnName, $params) {
		$this->raw("MODIFY COLUMN");
		$this->raw($this->shieldColumn($columnName));
		$this->raw($this->shield($params));
		
		return $this;
	}

	protected function dropColumn($columnName) {
		$this->raw("DROP COLUMN");
		$this->raw($this->shieldColumn($columnName));
		
		return $this;
	}

	protected function addIndex($columnName) {
		$columnName = $this->shieldColumn($columnName);
		$this->raw("ADD INDEX $columnName ($columnName)");
		
		return $this;
	}

	protected function dropIndex($columnName) {
		$this->raw("DROP INDEX");
		$this->raw($this->shieldColumn($columnName));
		
		return $this;
	}

	protected function addUnique() {
		$columnName = $this->shieldColumn($columnName);
		$this->raw("ADD UNIQUE $columnName ($columnName)");
		
		return $this;
	}

	protected function addForeignKey($columnName, $tableFor, $columnFor, $onDelete=false, $onUpdate=false) {
		$columnName = $this->shieldColumn($columnName);
		$tableFor = $this->shieldColumn($tableFor);
		$columnFor = $this->shieldColumn($columnFor);

		$this->raw("ADD FOREIGN KEY $columnName ($columnName)");
		$this->raw("REFERENCES $tableFor ($columnFor)");
		
		if($onDelete) {
			$onDelete = $this->shield($onDelete);
			$this->raw("ON DELETE $onDelete");
		}
		
		if($onUpdate) {
			$onUpdate = $this->shield($onUpdate);
			$this->raw("ON UPDATE $onUpdate");
		}

		return $this;
	}

	public function parseFilter($filterString='', array $filterFields=array(), array $filterOperators=array()) {
		if(!strlen($filterString)) return $this;

		$operators = array(
			'=' => '=',
			'>' => '>',
			'<' => '<',
			'>=' => '>=',
			'<=' => '<=',
			'!=' => '<>',
			'~' => 'LIKE',
		);

		$this->where();
		$this->openBracket();
		$this->openBracket();
		
		while(strlen($filterString)) {
			$skipChars = 1;

			switch($filterString[0]) {
				case '(':
					$this->openBracket();
					break;
				case ')':
					$this->closeBracket();
					break;
				case '&':
					if(!in_array('AND', $filterOperators)) {
						throw new QueryBuilderException('forbidden logic operator');
					}
					$this->and();
					break;
				case '|':
					if(!in_array('OR', $filterOperators)) {
						throw new QueryBuilderException('forbidden logic operator');
					}
					$this->or();
					break;
				default:
					if(preg_match("/^([a-zA-Z0-9_]+)([\=\>\<\~\!][\=]{0,1})([0-9]*)/", $filterString, $matches)) {
						$filterString = substr($filterString, strlen($matches[0]));
						$fieldName = $matches[1];
						$operator = $matches[2];
						$fieldValue = '';

						if($matches[3]) {
							$fieldValue = $matches[3];
						} else {
							if($filterString[0] !== "'") {
								throw new QueryBuilderException('start quote reqiured');
							} else {
								$filterString = substr($filterString, 1);
							}

							$endQuoteFound = false;
							while(strlen($filterString)) {
								$skipChars = 1;
								if($filterString[0] == "\\") {
									$fieldValue .= $filterString[1];
									$skipChars = 2;
								} elseif($filterString[0] == "'") {
									$endQuoteFound = true;
									$filterString = substr($filterString, $skipChars);
									break;
								} else {
									$fieldValue .= $filterString[0];
								}

								$filterString = substr($filterString, $skipChars);
							}
							if(!$endQuoteFound) {
								throw new QueryBuilderException('end quote reqiured');
							}
						}

						if(!isset($filterFields[$fieldName])) {
							throw new QueryBuilderException('forbidden filter field');
						} elseif(!in_array($operator, $filterFields[$fieldName])) {
							throw new QueryBuilderException('forbidden filter field operator ');
						}

						if(!isset($operators[$operator])) {
							throw new QueryBuilderException('bad operator');
						} else {
							$operator = $operators[$operator];
						}

						$this->condition($fieldName, $operator, $fieldValue);
					} else {
						throw new QueryBuilderException('bad condition');
					}
					$skipChars = 0;
					break;
			}

			if($skipChars) $filterString = substr($filterString, $skipChars);
		}

		$this->closeBracket();
		$this->closeBracket();

		return $this;
	}

	public function parseOrder($orderString='', array $orderFields=array()) {
		if(!strlen($orderString)) return $this;

		$orders = explode(',', $orderString);
		$orderBy = array();
		foreach($orders as $order) {
			if(!preg_match('/^([a-zA-Z0-9_]+)\:(asc|desc)$/', $order, $matches)) {
				throw new QueryBuilderException('bad order expression');
			} elseif(!isset($orderFields[$matches[1]])) {
				throw new QueryBuilderException('forbidden order field');
			} elseif(!in_array(strtoupper($matches[2]), $orderFields[$matches[1]])) {
				throw new QueryBuilderException('forbidden order field operator');
			} else {
				$orderBy[$matches[1]] = $matches[2];
			}
		}

		return $this->order($orderBy);
	}

	public function parseLimit(array $params, $maxListLimit=0) {
		if(isset($params['limit'])) {
			$limit = intval($params['limit']);
			if($maxListLimit && ($limit > $maxListLimit || $limit < 0)) {
				throw new QueryBuilderException('bad limit');
			}
			if(isset($params['page'])) {
				$page = intval($params['page']);
				if($page < 0) {
					throw new QueryBuilderException('bad page');
				}
				$this->limit($page*$limit, $limit);
			} else {
				$this->limit($limit);
			}
		} elseif(isset($params['page']) && $maxListLimit) {
			$page = intval($params['page']);
			if($page < 0) {
				throw new QueryBuilderException('bad page');
			}
			$this->limit($page*$maxListLimit, $maxListLimit);
		}

		return $this;
	}

	protected function comma() {
		$this->raw(",");
		
		return $this;
	}

	protected function openBracket() {
		$this->_bracketCount++;
		$this->raw(" (");
		
		return $this;
	}

	protected function continueBracket() {
		$re = '/\)[ ]*$/';
		if(preg_match($re, $this->_queryString)) {
			$this->_bracketCount++;
			$this->_queryString = preg_replace($re, '', $this->_queryString);
		}
		
		return $this;
	}

	protected function closeBracket() {
		$this->_bracketCount--;
		$this->raw(" )");
		
		return $this;
	}

	protected function condition($key, $operator, $value) {
		$this->raw($this->shieldColumn($key));
		$this->raw($this->shieldOperator($operator));
		
		if(preg_match('/^[A-Za-z0-9_]+\.[A-Za-z0-9_]+$/', $value)) {
			$this->raw($this->shieldColumn($value));
		} else {
			$this->raw($this->shieldValue($value));
		}
		
		return $this;
	}

	protected function shield($str) {
		return addslashes($str);
	}

	protected function shieldColumn($name, $asFlag=false) {
		if(!$name) return '';
		$arName = explode('.', $name);
		foreach($arName as &$val) {
			if($val == "*") {
				$val = $this->shield($val);
				$asFlag = false;
			} elseif(preg_match('/[A-Za-z0-9_]+\([A-Za-z0-9_\*]+\)/', $val)) {
				$val = $this->shield($val);
				$asFlag = false;
			} else {
				$val = "`".$this->shield($val)."`";
			}
		}
		$res = implode('.', $arName);
		if($asFlag) {
			$res .= " AS ".implode('_', str_replace('`', '', $arName));
		}
		
		return $res;
	}

	protected function shieldOperator($name) {
		return $this->shield($name);
	}

	protected function shieldValue($value) {
		return "'".$this->shield($value)."'";
	}
}

