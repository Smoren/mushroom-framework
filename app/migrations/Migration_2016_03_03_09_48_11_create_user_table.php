<?php

class Migration_2016_03_03_09_48_11_create_user_table extends Migration {
	public function up() {
		$this->createTable('users', array(
			'id' => 'int NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'email' => 'varchar(100) NOT NULL',
			'password' => 'varchar(50) NOT NULL',
			'hashAuth' => 'varchar(50) NOT NULL',
			'hashRestore' => 'varchar(50) NOT NULL',
			'name' => 'varchar(50) NOT NULL',
		));
	}
	public function down() {
		$this->table('users')->drop();
	}
}