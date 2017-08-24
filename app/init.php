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

Route::rest('/rest/test', 'RestTest', '[0-9a-f\-]+');
Route::rest('/rest/news', 'RestNews')->decorate('AccessManager', array('test' => 123));;
