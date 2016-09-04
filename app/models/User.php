<?php

class User extends UserModel {
	protected static $tableName = 'users';
	protected static $fields = array('id', 'email', 'password', 'hashAuth', 'hashRestore', 'name');
	protected $id;
	protected $email;
	protected $password;
	protected $hashAuth;
	protected $hashRestore;

	public static function validation() {
		return array();
	}
}