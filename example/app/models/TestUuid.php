<?php

class TestUuid extends UuidModel {
	const TABLE_NAME = 'testUuid';
	protected static $fields = array('id', 'title');

	public static function validation() {
		return array();
	}
}