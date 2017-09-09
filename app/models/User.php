<?php

class User extends UserModel {
	const TABLE_NAME = 'users';
	protected static $fields = array(static::PRIMARY_KEY, 'email', 'password', 'hashAuth', 'hashRestore', 'name');

	public static function validation() {
		return array();
	}
}