<?php

class News extends Model {
	const TABLE_NAME = 'news';
	protected static $fields = array('id', 'name', 'text');
	public static function validation() {
		return array(
			'name' => array(
				'required' => true,
				'rule' => '/^[A-Za-z ]+$/',
			),
			'text' => array(
				'required' => true,
				'rule' => function($value) {
					if(strlen($value) < 10) return false;
					return true;
				},
			),
		);
	}
}