<?php

use MushroomFramework\Models\UUIDModel;

class TestUuid extends UUIDModel {
	protected static $tableName = 'testUuid';
	protected static $fields = array('id', 'title');

	public static function validation() {
		return array();
	}
}