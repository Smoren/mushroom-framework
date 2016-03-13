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
Route::error('PageNotFound', function() {
	Response::status(404);
	return Response::text('404 страница не найдена');
});

// index
Route::any('/', 'Index@index');
Route::any('/test_model', 'Index@testModel');
Route::any('/user', 'Index@user');
Route::any('/rows', 'Index@rows');
Route::any('/products', 'Index@products');
Route::post('/product', 'Index@product');
Route::any('/list/{id}/{subId}/', function($id, $subId) {
	return Response::text("I'm element with id '$id' and subId '$subId'");
})->where(array(
	'id' => '[A-Za-z0-9_\-]+',
	'subId' => '[0-9]+',
));
Route::any('/event', function() {
	$arr = array();
	Event::trigger('test', $arr);
	print_r($arr);
	return Response::text('end');
});


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