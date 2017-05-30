<?php

use \MushroomFramework\Database\Migration;

class Migration_2017_05_30_16_42_29_add_news extends Migration {
	public function up() {
		$news = array(
			new News(array('name' => 'First news', 'text' => 'First news text')),
			new News(array('name' => 'Second news', 'text' => 'Second news text')),
			new News(array('name' => 'Third news', 'text' => 'Third news text')),
		);
		foreach($news as &$item) {
			$item->save();
		}
	}
	public function down() {
		$news = News::select()->getList();
		foreach($news as &$item) {
			$item->remove();
		}
	}
}