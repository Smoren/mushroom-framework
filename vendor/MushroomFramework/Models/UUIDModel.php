<?php

namespace MushroomFramework\Models;
use MushroomFramework\Database\Model;
use MushroomFramework\Pattern\Uuid;

abstract class UUIDModel extends Model {
	public static function find($id) {
		if(!($id instanceof Uuid)) {
			$id = new Uuid($id);
		}
		return parent::find($id->toBin());
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
}
