<?php

namespace MushroomFramework\Database;
use MushroomFramework\Database\Exceptions\ValidatorException;
use \Exception;

/**
 * Model Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
abstract class Model extends QueryBuilderAbstract {
	/**
	 * @const string TABLE_NAME Name of the model's table
	 */
	const TABLE_NAME = '';
	
	/**
	 * @const string PRIMARY_KEY Name primary key
	 */
	const PRIMARY_KEY = 'id';

	/**
	 * @var array $fields array of row's fields
	 */
	protected static $fields = array();

	/**
	 * @var array $row array of all the got fields from database
	 */
	protected $row = array();

	/**
	 * @var boolean $exists True if object exists in database
	 */
	protected $exists = false;

	/**
	 * @var Validator $validator Object for validation fields
	 */
	protected $validator;

	function __construct($row=false, $exists=false) {
		$this->exists = $exists;

		// отрабатываем событие
		if($this->exists) $this->onBeforeDataBound($row);

		// если передали поля, запишем их в объект
		if(is_array($row)) {
			$this->row = $row;
		}

		// добавляем атрибуты в объект
		foreach(static::$fields as $fieldName) {
			if(isset($row[$fieldName])) {
				$this->$fieldName = $row[$fieldName];
			} else {
				$this->$fieldName = null;
			}
		}

		// подключаем валидацию
		$this->validator = new Validator(static::$fields, static::validation());
		
		// отрабатываем событие
		if($this->exists) $this->onAfterDataBound($row);
	}

	function __get($attrName) {
		if(isset($this->$attrName)) {
			return $this->$attrName;
		}
		return null;
	}

	/**
	 * Console application's method for making new model with name=$name
	 * @throws Exception
	 * @param string $name Name of the model
	 * @return string $fileName
	 */
	public static function make($name) {
		$tableName = strtolower($name);
		$className = ucfirst($name);
		$fileName = "{$className}.php";
		if(is_file(MUSHROOM_DIR_APP_MODELS.'/'.$fileName)) {
			throw new Exception("file '$fileName' already exists");
		}
		$classString = "<?php\n\nclass $className extends Model {\n\tconst \$TABLE_NAME = '$tableName';\n\tprotected static \$fields = array('id');\n\n\tpublic static function validation() {\n\t\treturn array();\n\t}\n}";
		file_put_contents(MUSHROOM_DIR_APP_MODELS.'/'.$fileName, $classString);
		return $fileName;
	}

	/**
	 * Returns the array of the validation rules
	 * @return array
	 */
	public static function validation() {
		return array();
	}

	/**
	 * Find row form table by id and returns Model object of it or false if not exists
	 * @param int $id Id of the row
	 * @return mixed (false or Model)
	 */
	public static function find($id) {
		$row = static::select()->where(static::PRIMARY_KEY, '=', $id)->exec()->fetch();
		if(!$row) return false;
		return new static($row, true);
	}

	/**
	 * Find rows form table by $fieldName and returns QueryBuilder object
	 * @param string $fieldName Name of the field searching by
	 * @param mixed $value Value of the field searching by
	 * @return QueryBuilder
	 */
	public static function findBy($fieldName, $value) {
		$result = static::select()->where($fieldName, '=', $value);
		return $result;
	}

	/**
	 * Starts SELECT sql query from the model's table
	 * @param array ...$fields List of the selecting fields
	 * @return QueryBuilder
	 */
	public static function select(...$fields) {
		if(!sizeof($fields)) $fields = array('*');
		// if(!in_array("*", $fields) && !in_array(static::TABLE_NAME.".*", $fields)) {
		// 	$fields[] = static::TABLE_NAME.".*";
		// }
		return parent::select(...$fields)->from(static::TABLE_NAME);
	}

	/**
	 * Finishs the sql query, executes it and returns array of Model objects
	 * @return array
	 */
	public function getList() {
		$result = array();
		$rs = $this->exec();
		while($row = $rs->fetch()) {
			$result[] = new static($row, true);
		}
		return $result;
	}

	/**
	 * Finishs the sql query, executes it and returns array of Model objects
	 * @return array
	 */
	public function list() {
		return $this->getList();
	}

	/**
	 * Finishs the sql query, executes it and returns array of row's arrays
	 * @return array
	 */
	public function getArrayList() {
		$result = array();
		$rs = $this->exec();
		while($row = $rs->fetch()) {
			$result[] = $row;
		}
		return $result;
	}

	/**
	 * Finishs the sql query, executes it and returns array of row's arrays
	 * @return array
	 */
	public function arrayList() {
		return $this->getArrayList();
	}

	/**
	 * Finishs the sql query, executes it and returns Model of the first found row or false ii rows are not found
	 * @return mixed (Model or false)
	 */
	public function getFirst() {
		$row = $this->exec()->fetch();
		if($row) return new static($row, true);
		return false;
	}

	/**
	 * Finishs the sql query, executes it and returns Model of the first found row or false ii rows are not found
	 * @return mixed (Model or false)
	 */
	public function first() {
		return $this->getFirst();
	}

	/**
	 * Insert or update (depends of $this->exists) object as a row of model's table
	 * @return $this
	 */
	public function save($validate=true) {
		$primaryKey = static::PRIMARY_KEY;
		$fields = array();
		foreach(static::$fields as $fieldName) {
			if(isset($this->$fieldName)) {
				$fields[$fieldName] = $this->$fieldName;
			}
		}

		$this->onBeforeSave($fields);

		if($validate) {
			$validator = $this->validate($fields);
			if($validator->error()) {
				throw new ValidatorException($validator->getErrorFields());
			}
		}

		if($this->exists) {
			// если объект присутствует в БД, обновляем его
			$id = $fields[$primaryKey] ? $fields[$primaryKey] : $this->$primaryKey;
			static::update(static::TABLE_NAME, $fields)->where(static::PRIMARY_KEY, '=', $id)->exec();
		} else {
			// иначе добавляем его в БД
			$insertedId = static::insert(static::TABLE_NAME, $fields)->exec()->getInsertedId();
			if($insertedId) $this->$primaryKey = $insertedId;
			$this->exists = true;
		}

		$this->onAfterSave($fields);

		return $this;
	}

	/**
	 * Removes object from table
	 * @return $this
	 */
	public function remove($tableName=false) {
		if(!$this->exists) return false;
		$primaryKey = static::PRIMARY_KEY;
		$tableName = $tableName ? $tableName : static::TABLE_NAME;
		$this->exists = false;
		static::delete($tableName)->where(static::PRIMARY_KEY, '=', $this->$primaryKey)->exec();
		return $this;
	}

	/**
	 * Launchs validation of $data and returns result of it
	 * @return Validator
	 */
	public function validate(array $data) {
		return $this->validator->validate($data);
	}

	/**
	 * Setup fields' values with or without validation (depends of $validate)
	 * @param array $data
	 * @param boolean $validate
	 * @return Validator
	 */
	public function set(array $data, $validate=false) {
		if(!$validate || $this->validate($data)->success()) {
			foreach($data as $fieldName => $value) {
				if(in_array($fieldName, static::$fields)) $this->$fieldName = $value;
			}
		}
		if(!$validate) $this->validator->reset();
		return $this->validator;
	}

	/**
	 * Handler of the data getting from DB event
	 * @return void
	 */
	protected function onBeforeDataBound(&$fields) {

	}

	/**
	 * Handler of the data getting from DB event
	 * @return void
	 */
	protected function onAfterDataBound(&$fields) {

	}

	/**
	 * Handler of the data setting data by method $this->save()
	 * @return void
	 */
	protected function onBeforeSave(&$fields) {

	}

	/**
	 * Handler of the data setting data by method $this->save()
	 * @return void
	 */
	protected function onAfterSave(&$fields) {

	}

	/**
	 * Returns get ID
	 * @return mixed $id
	 */
	public function getId() {
		$primaryKey = static::PRIMARY_KEY;
		return $this->$primaryKey;
	}

	/**
	 * Returns all object's fields as array
	 * @return array $result
	 */
	public function asArray() {
		$result = array();
		foreach(static::$fields as $key) {
			$result[$key] = $this->$key;
		}
		return $result;
	}
}

