<?php

use \MushroomFramework\Main\App;

if(function_exists('session_start')) {
	// инициализируем сессию
	session_start();
}

// объявляем константы
define('MUSHROOM_DIR_ROOT', dirname(__DIR__));
define('MUSHROOM_DIR_LIB', MUSHROOM_DIR_ROOT.DIRECTORY_SEPARATOR.'vendor');
define('MUSHROOM_DIR_CORE', MUSHROOM_DIR_ROOT.DIRECTORY_SEPARATOR.'core');
define('MUSHROOM_DIR_PUBLIC', MUSHROOM_DIR_ROOT.DIRECTORY_SEPARATOR.'public');
define('MUSHROOM_DIR_PUBLIC_UPLOAD', MUSHROOM_DIR_PUBLIC.DIRECTORY_SEPARATOR.'upload');
define('MUSHROOM_DIR_APP', MUSHROOM_DIR_ROOT.DIRECTORY_SEPARATOR.'app');
define('MUSHROOM_DIR_APP_VIEWS', MUSHROOM_DIR_APP.DIRECTORY_SEPARATOR.'views');
define('MUSHROOM_DIR_APP_CONTROLLERS', MUSHROOM_DIR_APP.DIRECTORY_SEPARATOR.'controllers');
define('MUSHROOM_DIR_APP_MODELS', MUSHROOM_DIR_APP.DIRECTORY_SEPARATOR.'models');
define('MUSHROOM_DIR_APP_CONFIG', MUSHROOM_DIR_APP.DIRECTORY_SEPARATOR.'config');
define('MUSHROOM_DIR_APP_MIGRATIONS', MUSHROOM_DIR_APP.DIRECTORY_SEPARATOR.'migrations');


if(is_file(MUSHROOM_DIR_LIB.DIRECTORY_SEPARATOR.'autoload.php')) {
	// подключаем autoloader от composer, если он есть
	$loader = include_once(MUSHROOM_DIR_LIB.DIRECTORY_SEPARATOR.'autoload.php');
	$loader->addPsr4('', array(MUSHROOM_DIR_LIB, MUSHROOM_DIR_APP_CONTROLLERS, MUSHROOM_DIR_APP_MODELS));
} else {
	// подключаем autoloader
	require_once(MUSHROOM_DIR_CORE.DIRECTORY_SEPARATOR.'autoloader.php');
}

\MushroomFramework\Main\App::gi()->init();