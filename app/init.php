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
Route::error('NotFound', 'Error.notFound');

// index
Route::register('/', 'Index.index');
Route::register('/news/{id}', 'Index.detail')->where(array(
	'id' => '[0-9]+',
));
Route::register('/test/method', 'Test.method');
Route::register('/test/transfer', 'Test.transfer');
Route::register('/test/test', 'Test.test');

Route::rest('/rest/test', 'RestTest', '[0-9a-f\-]+');
Route::rest('/rest/news', 'RestNews');
// Route::register('/rest', 'TestRest.collection');
// Route::register('/rest/{id}', 'TestRest.item')->where(array(
// 	'id' => '[a-f0-9\-]+'
// ));






// Route::register('/test_model', 'Index@testModel');
// Route::register('/user', 'Index@user');
// Route::register('/rows', 'Index@rows');
// Route::register('/products', 'Index@products');
// Route::register('/product', 'Index@product');
// Route::register('/list/{id}/{subId}/', function($id, $subId) {
// 	return Response::text("I'm element with id '$id' and subId '$subId'");
// })->where(array(
// 	'id' => '[A-Za-z0-9_\-]+',
// 	'subId' => '[0-9]+',
// ));
// Route::register('/event', function() {
// 	$arr = array();
// 	Event::trigger('test', $arr);
// 	print_r($arr);
// 	return Response::text('end');
// });


/*

// /list/*
Route::any('/list/', 'List@index');
Route::any('/list/{id}/', 'List@element')->where(array(
	'id' => '[A-Za-z0-9_\-]+',
));
Route::any('/list/{id}/{subId}/', function($id, $subId) {
	return Response::text("I'm element with id '$id' and subId '$subId'");
})->where(array(
	'id' => '[A-Za-z0-9_\-]+',
	'subId' => '[0-9]+',
));

// other
Route::any('/mylist/', 'Index@mylist');

Route::any('/test/', function() {
	return Response::text("I'm a test");
});

*/