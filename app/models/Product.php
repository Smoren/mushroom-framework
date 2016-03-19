<?php

class Product extends Model {
	protected static $tableName = 'products';
	protected static $fields = array('id', 'name', 'description', 'price', 'soldOut', 'reviews');
	
	public static function validation() {
		return array();
	}

	protected function onSave(&$fields) {
		$fields['reviews'] = serialize($fields['reviews']);
	}

	protected function onGet(&$fields) {
		$fields['soldOut'] = boolval($fields['soldOut']);
		$fields['reviews'] = unserialize($fields['reviews']);
	}
}