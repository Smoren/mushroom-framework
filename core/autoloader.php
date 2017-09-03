<?php

function autoloader($className) {
	$arPath = explode('\\', $className);
	$pathRoot = $arPath[0];
	$pathes = array();
	$arDirs = array('models', 'views', 'controllers');
	$pathes[] = MUSHROOM_DIR_LIB.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $arPath).'.php';
	foreach($arDirs as $dir) {
		$pathes[] = MUSHROOM_DIR_APP.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $arPath).'.php';
	}
	foreach($pathes as $path) {
		if(file_exists($path)) {
			include_once($path);
			break;
		}
	}
}

spl_autoload_register(null, false);
spl_autoload_extensions(".php");
spl_autoload_register('autoloader');
