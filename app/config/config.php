<?php

return array(
	'database' => include('config.db.php'),
	'defaultRouting' => false,
	'debug' => true,
	'classAliases' => array(
		'App' => '\MushroomFramework\Facades\App',
		'Route' => '\MushroomFramework\Facades\Route',
		'Router' => '\MushroomFramework\Facades\Router',
		'Uri' => '\MushroomFramework\Facades\Uri',
		'Response' => '\MushroomFramework\Facades\Response',
		'Request' => '\MushroomFramework\Facades\Request',
		'Session' => '\MushroomFramework\Facades\Session',
		'Cookie' => '\MushroomFramework\Facades\Cookie',
		'Form' => '\MushroomFramework\Facades\Form',
		'Html' => '\MushroomFramework\Facades\Html',
		'Event' => '\MushroomFramework\Facades\Event',
		'Controller' => '\MushroomFramework\Routing\Controller',
		'ControllerDecorator' => '\MushroomFramework\Routing\ControllerDecorator',
		'DatabaseSession' => '\MushroomFramework\ORMushroom\DatabaseSession',
		'QueryBuilder' => '\MushroomFramework\ORMushroom\QueryBuilder',
		'Model' => '\MushroomFramework\ORMushroom\Model',
		'View' => '\MushroomFramework\View\View',
		'UserModel' => '\MushroomFramework\Extensions\Models\User',
		'UuidModel' => '\MushroomFramework\Extensions\Models\UuidModel',
		'RestController' => '\MushroomFramework\Extensions\Controllers\RestController',
	),
);