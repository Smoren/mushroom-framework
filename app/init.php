<?php

/******************************
------Глобальные объекты-------
******************************/

App::setProperty('test', 'test_value');

/******************************
-----------События-------------
******************************/

Event::register('test', function(&$data) {
	echo "firstHandler\n";
	$data['testEvent'] = 'triggered';
});

Event::register('test', function($data=null) {
	echo "secondHandler\n";
});

Event::register('another', function($data=null) {
	echo "anotherHandler\n";
});

Event::register('onAfterInitIncluded', function($app) {
	// $dbConfig = $app->getConfig()['database'];
	// // $dbConfig['type'] = '123';
	// $dbm = new \MushroomFramework\ORMushroom\DatabaseSession($dbConfig, true);
	// $qs = \MushroomFramework\ORMushroom\QueryBuilder::select()
	// 	->from('news')
	// 	->where('id', '=', 4);
	// // $qs = \MushroomFramework\ORMushroom\Mysql\QueryBuilder::select()
	// // 	->from('news')
	// // 	->where('id', '=', 1);
	// echo $qs;
	// echo '<pre>'; print_r($qs->exec($dbm)->fetch()); echo '</pre>';
	// die();
});


/******************************
-----------Маршруты------------
******************************/

// error 404
Route::error(404, 'Error.notFound');

// index
Route::register('/', 'Index.index');
Route::register('/news/{id}', 'Index.detail')->where(array(
	'id' => '[0-9]+',
))->decorate('AccessManager', array('test' => 123));
Route::register('/test/method', 'Test.method');
Route::register('/test/transfer', 'Test.transfer');
Route::register('/test/test', 'Test.test');

// Rest routes
Route::rest('/rest/test', 'Rest\Test', '[0-9a-f\-]+');
Route::rest('/rest/news', 'Rest\News')->decorate('AccessManager', array('test' => 123));