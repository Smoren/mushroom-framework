<?php

class TestUuid extends UuidModel {
	protected static $tableName = 'testUuid';
	protected static $fields = array('id', 'title');

	public static function validation() {
		return array();
	}
}