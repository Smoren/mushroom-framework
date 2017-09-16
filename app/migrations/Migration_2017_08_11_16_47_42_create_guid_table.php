<?php

class Migration_2017_08_11_16_47_42_create_guid_table extends Migration {
	public function up() {
		$this->createTable('testUuid', array(
			'id' => 'BINARY(16) NOT NULL PRIMARY KEY',
			'title' => 'varchar(100) NOT NULL'
		));
	}
	public function down() {
		try {
			$this->table('testUuid')->drop();
		} catch(Exception $e) {}
	}
}