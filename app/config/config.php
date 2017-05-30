<?php

return array(
	'database' => include('config.db.php'),
	'defaultRouting' => false,
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
		'QueryBuilder' => '\MushroomFramework\Database\QueryBuilder',
		'Event' => '\MushroomFramework\Facades\Event',
		'Controller' => '\MushroomFramework\Routing\Controller',
		'Model' => '\MushroomFramework\Database\Model',
		'View' => '\MushroomFramework\View\View',
		'UserModel' => '\MushroomFramework\Models\User',
	),
);