<?php

namespace MushroomFramework\ORMushroom\Abstract;
use MushroomFramework\ORMushroom\Exceptions\QueryBuilderException;

/**
 * QueryBuilder Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
abstract class QueryBuilder {
	/**
	 * @var string $encoding Encoding
	 */
	protected static $encoding = 'utf8';

	/**
	 * @var string $collate Collate
	 */
	protected static $collate = 'utf8_general_ci';

	/**
	 * @var string $queryString Collate
	 */
	protected $queryString = '';

	/**
	 * Sets encoding and collate
	 * @param string $encoding
	 * @param string $collate
	 * @return void
	 */
	public static function setEncoding($encoding, $collate) {
		static::$encoding = $encoding;
		static::$collate = $collate;
	}

	/**
	 * Add slashes for protecting from sql-injections
	 * @param string $str
	 * @return string
	 */
	public static function shield($str) {
		return addslashes($str);
	}

	/**
	 * Starts query by setting $str as $this->queryString
	 * @param string $str
	 * @return QueryBuilder
	 */
	public static function startQuery($str) {
		$qb = new static;
		$qb->queryString = $str;
		return $qb;
	}

	/**
	 * Adds $str as $this->queryString
	 * @param string $str
	 * @return QueryBuilder
	 */
	public function addToQuery($str) {
		$this->queryString .= " $str ";
		return $this;
	}

	/**
	 * Adds $str as $this->queryString
	 * @param string $str
	 * @return QueryBuilder
	 */
	public function raw($str) {
		return $this->addToQuery($str);
	}

	/**
	 * Returns query string
	 * @return string
	 */
	public function getQueryString() {
		return str_replace('  ', ' ', $this->queryString);
	}

	function __toString() {
		return $this->getQueryString();
	}

	/**
	 * Executes query and returns QueryResult object
	 * @param DatabaseManager $databaseManager (if false static::$databaseManager is in use)
	 * @return QueryResult
	 */
	public function exec($databaseManager) {
		return $databaseManager->query($this->queryString);
	}

	/**
	 * Starts SELECT sql query
	 * @param array ...$data List of the selecting fields
	 * @return QueryBuilder
	 */
	public static function select(...$select) {
		if(!sizeof($select)) $select = array('*');
		$qb = static::startQuery('SELECT');
		foreach($select as &$val) {
			$val = $qb::formatColName($val, true);
		}
		$qb->addToQuery(implode(', ', $select));
		
		return $qb;
	}

	/**
	 * Makes INSERT sql query
	 * @param string $tableName
	 * @param array $fields
	 * @return QueryBuilder
	 */
	public static function insert($tableName, $fields) {
		$qb = static::startQuery("INSERT INTO $tableName");
		$colNames = array();
		$values = array();
		foreach($fields as $colName => $value) {
			$colNames[] = $colName;
			$values[] = $value;
		}

		foreach($colNames as &$val) {
			$val = $qb::formatColName($val);
		}
		foreach($values as &$val) {
			$val = $qb->formatValue($val);
		}

		$qb->addToQuery('(');
		$qb->addToQuery(implode(', ', $colNames));
		$qb->addToQuery(')');

		$qb->addToQuery('VALUES (');
		$qb->addToQuery(implode(', ', $values));
		$qb->addToQuery(')');

		return $qb;
	}

	/**
	 * Starts UPDATE sql query
	 * @param string $tableName
	 * @param array $fields
	 * @return QueryBuilder
	 */
	// создает запрос UPDATE
	public static function update($tableName, $fields) {
		foreach($fields as $colName => $val) {
			$fields[$colName] = static::formatColName($colName)." = ".static::formatValue($val);
		}
		$qb = static::startQuery("UPDATE ".static::formatColName($tableName)." SET ");
		$qb->addToQuery(implode(', ', $fields));
		return $qb;
	}

	/**
	 * Makes CREATE TABLE sql query
	 * @param string $tableName
	 * @param array $fields
	 * @return QueryBuilder
	 */
	public static function createTable($tableName, $fields) {
		$qb = static::startQuery("CREATE TABLE IF NOT EXISTS ".static::formatColName($tableName)." (");
		foreach($fields as $colName => $params) {
			$fields[$colName] = static::formatColName($colName)." $params";
		}
		$qb->addToQuery(implode(', ', $fields));
		$qb->addToQuery(") CHARACTER SET ".static::formatValue(static::$encoding)." COLLATE ".static::formatValue(static::$collate).";");
		return $qb;
	}

	/**
	 * Starts DELETE sql query
	 * @param string $tableName
	 * @return QueryBuilder
	 */
	public static function delete($tableName) {
		$qb = static::startQuery("DELETE FROM ".static::formatColName($tableName));
		return $qb;
	}

	/**
	 * Makes FROP TABLE sql query
	 * @param string $tableName
	 * @return QueryBuilder
	 */
	public static function dropTable($tableName) {
		$qb = static::startQuery("DROP TABLE ".static::formatColName($tableName));
		return $qb;
	}

	/**
	 * Starts ALTER TABLE sql query
	 * @param string $tableName
	 * @return QueryBuilder
	 */
	public static function alterTable($tableName) {
		$qb = static::startQuery("ALTER TABLE ".static::formatColName($tableName));
		return $qb;
	}

	/**
	 * Continues ALTER TABLE with ADD COLUMN
	 * @param string $colName
	 * @param array $params
	 * @return $this
	 */
	public function addColumn($colName, $params) {
		$this->addToQuery("ADD COLUMN ".static::formatColName($colName)." $params");
		return $this;
	}

	/**
	 * Continues ALTER TABLE with MODIFY/EDIT COLUMN
	 * @param string $colName
	 * @param array $params
	 * @return $this
	 */
	public function editColumn($colName, $params) {
		$this->addToQuery("MODIFY COLUMN ".static::formatColName($colName)." $params");
		return $this;
	}

	/**
	 * Continues ALTER TABLE with DROP COLUMN
	 * @param string $colName
	 * @return $this
	 */
	public function dropColumn($colName) {
		$this->addToQuery("DROP COLUMN ".static::formatColName($colName));
		return $this;
	}

	/**
	 * Continues ALTER TABLE with ADD INDEX
	 * @param string $colName
	 * @return $this
	 */
	public function addIndex($colName) {
		$colName = static::formatColName($colName);
		$this->addToQuery("ADD INDEX $colName ($colName)");
		return $this;
	}

	/**
	 * Continues ALTER TABLE with DROP INDEX
	 * @param string $colName
	 * @return $this
	 */
	public function dropIndex($colName) {
		$this->addToQuery("DROP INDEX ".static::formatColName($colName));
		return $this;
	}

	/**
	 * Continues ALTER TABLE with ADD UNIQUE
	 * @param string $colName
	 * @return $this
	 */
	public function addUnique($colName) {
		$colName = static::formatColName($colName);
		$this->addToQuery("ADD UNIQUE $colName ($colName)");
		return $this;
	}

	/**
	 * Continues ALTER TABLE with ADD FOREIGN KEY
	 * @param string $colName
	 * @param string $forTable
	 * @param string $forColumn
	 * @param string $onDelete
	 * @param string $onUpdate
	 * @return $this
	 */
	public function addForeignKey($colName, $forTable, $forColumn, $onDelete=false, $onUpdate=false) {
		$colName = static::formatColName($colName);
		$forTable = static::formatColName($forTable);
		$forColumn = static::formatColName($forColumn);
		$this->addToQuery("ADD FOREIGN KEY $colName ($colName)");
		$this->addToQuery("REFERENCES $forTable ($forColumn)");
		if($onDelete) {
			$this->addToQuery("ON DELETE $onDelete");
		}
		if($onUpdate) {
			$this->addToQuery("ON UPDATE $onUpdate");
		}
		return $this;
	}

	/**
	 * Adds comma to $this->queryString
	 * @return $this
	 */
	public function comma() {
		$this->addToQuery(",");
		return $this;
	}

	/**
	 * Makes sql query to show tables list
	 * @param string $like
	 * @return QuwetBuilder
	 */
	static function showTables($like="") {
		$qb = static::startQuery("SHOW TABLES ");
		if($like) $qb->addToQuery("LIKE ".static::formatValue($like));
		return $qb;
	}

	/**
	 * Continues SELECT query with FROM
	 * @param string $tableName
	 * @param string $as
	 * @return $this
	 */
	public function from($tableName, $as='') {
		$this->addToQuery("FROM ".static::formatColName($tableName));
		if($as) $this->addToQuery("AS ".static::shield($as));
		return $this;
	}

	/**
	 * Starts bracket operation
	 * @return $this
	 */
	public function startBracketOperation() {
		$this->addToQuery(" (");
		return $this;
	}

	/**
	 * Continues bracket operation
	 * @return $this
	 */
	public function continueBracketOperation() {
		$this->queryString = preg_replace('/\)[ ]*$/', '', $this->queryString);
	}

	/**
	 * Ends bracket operation
	 * @return $this
	 */
	public function endBracketOperation() {
		$this->addToQuery(")");
	}

	/**
	 * Adds condition to $this->queryString
	 * @param string $key
	 * @param string $sign
	 * @param mixed $val
	 * @return $this
	 */
	public function condition($key, $sign, $val) {
		$this->addToQuery(static::formatColName($key));
		$this->addToQuery(static::shield($sign));
		if(preg_match('/^[A-Za-z0-9_]+\.[A-Za-z0-9_]+$/', $val)) {
			$this->addToQuery(static::formatColName($val));
		} else {
			$this->addToQuery(static::formatValue($val));
		}
	}

	/**
	 * Adds WHERE condition to $this->queryString
	 * @param string $key
	 * @param string $sign
	 * @param mixed $val
	 * @return $this
	 */
	public function where($key, $sign, $val) {
		$this->addToQuery("WHERE");
		$this->startBracketOperation();
		$this->condition($key, $sign, $val);
		$this->endBracketOperation();
		return $this;
	}

	/**
	 * Adds AND WHERE condition to $this->queryString
	 * @param string $key
	 * @param string $sign
	 * @param mixed $val
	 * @return $this
	 */
	public function andWhere($key, $sign, $val) {
		$this->continueBracketOperation();
		$this->addToQuery("AND");
		$this->condition($key, $sign, $val);
		$this->endBracketOperation();
		return $this;
	}

	/**
	 * Adds OR WHERE condition to $this->queryString
	 * @param string $key
	 * @param string $sign
	 * @param mixed $val
	 * @return $this
	 */
	public function orWhere($key, $sign, $val) {
		$this->continueBracketOperation();
		$this->addToQuery("OR");
		$this->condition($key, $sign, $val);
		$this->endBracketOperation();
		return $this;
	}

	/**
	 * Adds ON condition to $this->queryString
	 * @param string $key
	 * @param string $sign
	 * @param mixed $val
	 * @return $this
	 */
	public function on($key, $sign, $val) {
		$this->addToQuery("ON");
		$this->startBracketOperation();
		$this->condition($key, $sign, $val);
		$this->endBracketOperation();
		return $this;
	}

	/**
	 * Adds AND ON condition to $this->queryString
	 * @param string $key
	 * @param string $sign
	 * @param mixed $val
	 * @return $this
	 */
	public function andOn($key, $sign, $val) {
		return $this->andWhere($key, $sign, $val);
	}

	/**
	 * Adds OR ON condition to $this->queryString
	 * @param string $key
	 * @param string $sign
	 * @param mixed $val
	 * @return $this
	 */
	public function orOn($key, $sign, $val) {
		return $this->orWhere($key, $sign, $val);
	}

	/**
	 * Adds ORDER BY to $this->queryString
	 * @param array $order
	 * @return $this
	 */
	public function orderBy($order) {
		foreach($order as $colName => $val) {
			$order[$colName] = static::formatColName($colName)." ".static::shield($val);
		}
		$this->addToQuery("ORDER BY ".implode(', ', $order));
		return $this;
	}

	/**
	 * Adds LIMIT to $this->queryString
	 * @param int $num1
	 * @param int $num2
	 * @return $this
	 */
	public function limit($num1, $num2=false) {
		$this->addToQuery("LIMIT ".intval($num1));
		if(!($num2 === false)) $this->addToQuery(", ".intval($num2));
		return $this;
	}

	/**
	 * Adds JOIN to $this->queryString
	 * @param string $tableName
	 * @param string $as
	 * @return $this
	 */
	public function join($tableName, $as='') {
		$this->addToQuery("JOIN ".static::formatColName($tableName)." ".static::shield($as));
		return $this;
	}

	/**
	 * Adds LEFT JOIN to $this->queryString
	 * @param string $tableName
	 * @param string $as
	 * @return $this
	 */
	public function leftJoin($tableName, $as='') {
		$this->addToQuery("LEFT");
		return $this->join($tableName, $as);
	}

	/**
	 * Adds RIGHT JOIN to $this->queryString
	 * @param string $tableName
	 * @param string $as
	 * @return $this
	 */
	public function rightJoin($tableName, $as='') {
		$this->addToQuery("RIGHT");
		return $this->join($tableName, $as);
	}

	/**
	 * Shields col name
	 * @param string $str
	 * @param string $addAs
	 * @return string
	 */
	public static function formatColName($str, $addAs=false) {
		if(!$str) return '';
		$arStr = explode('.', $str);
		foreach($arStr as &$val) {
			if($val == "*") {
				$val = static::shield($val);
				$addAs = false;
			} elseif(preg_match('/[A-Za-z0-9_]+\([A-Za-z0-9_\*]+\)/', $val)) {
				$val = static::shield($val);
				$addAs = false;
			} else {
				$val = "`".static::shield($val)."`";
			}
		}
		$res = implode('.', $arStr);
		if($addAs) $res .= " AS ".implode('_', str_replace('`', '', $arStr));
		return $res;
	}

	/**
	 * Shields col value
	 * @param mixed $str
	 * @return string
	 */
	public static function formatValue($str) {
		return "'".static::shield($str)."'";
	}

	/**
	 * Makes where-conditions from string like colname1=value1&(colname2~value2|colname3>value3)
	 * @param string $filterString
	 * @param array $filterFields
	 * @param array $filterOperators
	 * @return QueryBuilder
	 */
	public function parseFilter($filterString='', $filterFields=array(), $filterOperators=array()) {
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

		$this->addToQuery("WHERE");
		$this->startBracketOperation();
		$this->startBracketOperation();
		
		$bracketsLevel = 0;
		while(strlen($filterString)) {
			$skipChars = 1;

			switch($filterString[0]) {
				case '(':
					$bracketsLevel++;
					$this->startBracketOperation();
					break;
				case ')':
					$bracketsLevel--;
					$this->endBracketOperation();
					break;
				case '&':
					if(!in_array('AND', $filterOperators)) {
						throw new QueryBuilderException('forbidden logic operator');
					}
					$this->addToQuery("AND");
					break;
				case '|':
					if(!in_array('OR', $filterOperators)) {
						throw new QueryBuilderException('forbidden logic operator');
					}
					$this->addToQuery("OR");
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

		if($bracketsLevel) {
			throw new QueryBuilderException('bad brackets');
		}
	
		$this->endBracketOperation();
		$this->endBracketOperation();

		return $this;
	}

	/**
	 * Makes order-conditions from string like colname1:asc,colname2:desc
	 * @param string $orderString
	 * @param array $orderFields
	 * @return QueryBuilder
	 */
	public function parseOrder($orderString='', $orderFields=array()) {
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

		return $this->orderBy($orderBy);
	}

	/**
	 * Makes limit-condition from params array like array('limit' => 10, 'page' => 2)
	 * @param array $params
	 * @return QueryBuilder
	 */
	public function parseLimit($params) {
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
}
