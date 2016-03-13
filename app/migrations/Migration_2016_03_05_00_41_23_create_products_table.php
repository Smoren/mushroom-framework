<?php

use \MushroomFramework\Database\Migration;

class Migration_2016_03_05_00_41_23_create_products_table extends Migration {
	public function up() {
		$this->createTable('products', array(
			'id' => 'int NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'name' => 'varchar(100) NOT NULL',
			'description' => 'text',
			'price' => 'int(10) NOT NULL',
			'soldOut' => 'tinyint(1)',
			'reviews' => 'text',
		));
	}
	public function down() {
		$this->table('products')->drop();
	}
}