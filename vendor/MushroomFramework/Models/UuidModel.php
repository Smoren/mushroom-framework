<?php

namespace MushroomFramework\Models;
use MushroomFramework\Database\Model;
use MushroomFramework\Pattern\Uuid;

abstract class UuidModel extends Model {
	public static function find($id) {
		if(!($id instanceof Uuid)) {
			$id = new Uuid($id);
		}
		return parent::find($id->toBin());
	}

	public function getArrayList() {
		$primaryKey = static::$primaryKey;
		$result = array();
		$rs = $this->exec();
		while($row = $rs->fetch()) {
			$row[$primaryKey] = (string)Uuid::fromBin($row[$primaryKey]);
			$result[] = $row;
		}
		return $result;
	}

	protected function onBeforeDataBound(&$fields) {
		$fields[static::$primaryKey] = Uuid::fromBin($fields[static::$primaryKey]);
		parent::onBeforeDataBound($fields);
	}

	protected function onBeforeSave(&$fields) {
		$primaryKey = static::$primaryKey;
		if(!$this->exists && !$this->$primaryKey) {
			$this->$primaryKey = Uuid::generate();
		}

		if(!($this->$primaryKey instanceof Uuid)) {
			$this->$primaryKey = new Uuid($this->$primaryKey);
		}

		$fields[$primaryKey] = $this->$primaryKey->toBin();
		parent::onBeforeSave($fields);
	}

	public function getId() {
		$primaryKey = static::$primaryKey;
		return (string)$this->$primaryKey;
	}

	public function asArray() {
		$primaryKey = static::$primaryKey;
		$result = parent::asArray();
		$result[$primaryKey] = (string)$result[$primaryKey];
		return $result;
	}

	public function remove($tableName=false) {
		if(!$this->exists) return false;
		$primaryKey = static::$primaryKey;
		$tableName = $tableName ? $tableName : static::$tableName;
		$this->exists = false;
		static::delete($tableName)->where(static::$primaryKey, '=', $this->$primaryKey->toBin())->exec();
		return $this;
	}
}
