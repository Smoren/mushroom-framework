<?php

class Product extends Model {
	protected static $tableName = 'products';
	protected static $fields = array('id', 'name', 'description', 'price', 'soldOut', 'reviews');
	
	public static function validation() {
		return array();
	}

	function onSet() {
		$this->reviews = serialize($this->reviews);
	}

	function onGet() {
		$this->soldOut = boolval($this->soldOut);
		$this->reviews = unserialize($this->reviews);
	}
}