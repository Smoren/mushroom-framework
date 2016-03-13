<?php

use \MushroomFramework\Database\Migration;

class Migration_2016_03_01_00_42_52_create_news_table extends Migration {
	public function up() {
		$this->createTable('news', array(
			'id' => 'int NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'name' => 'varchar(50) NOT NULL',
			'text' => 'text NOT NULL',
		));
	}
	public function down() {
		$this->table('news')->drop();
	}
}