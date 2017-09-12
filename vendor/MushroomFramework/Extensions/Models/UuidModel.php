<?php

namespace MushroomFramework\Extensions\Models;
use MushroomFramework\ORMushroom\Model;
use MushroomFramework\Pattern\Uuid;

abstract class UuidModel extends Model {
	public static function find($id) {
		if(!($id instanceof Uuid)) {
			$id = new Uuid($id);
		}
		return parent::find($id->toBin());
	}

	public function getArrayList() {
		$primaryKey = static::PRIMARY_KEY;
		$result = array();
		$rs = $this->exec();
		while($row = $rs->fetch()) {
			$row[$primaryKey] = (string)Uuid::fromBin($row[$primaryKey]);
			$result[] = $row;
		}
		return $result;
	}

	protected function onBeforeDataBound(&$fields) {
		$fields[static::PRIMARY_KEY] = Uuid::fromBin($fields[static::PRIMARY_KEY]);
		parent::onBeforeDataBound($fields);
	}

	protected function onBeforeSave(&$fields) {
		$primaryKey = static::PRIMARY_KEY;
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
		$primaryKey = static::PRIMARY_KEY;
		return (string)$this->$primaryKey;
	}

	public function asArray() {
		$primaryKey = static::PRIMARY_KEY;
		$result = parent::asArray();
		$result[$primaryKey] = (string)$result[$primaryKey];
		return $result;
	}

	public function remove($tableName=false) {
		if(!$this->exists) return false;
		$primaryKey = static::PRIMARY_KEY;
		$tableName = $tableName ? $tableName : static::TABLE_NAME;
		$this->exists = false;
		static::delete($tableName)->where(static::PRIMARY_KEY, '=', $this->$primaryKey->toBin())->exec();
		return $this;
	}
}
