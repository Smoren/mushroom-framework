<?php

function autoloader($className) {
	$arPath = explode('\\', $className);
	$pathRoot = $arPath[0];
	$pathes = array();
	if($pathRoot == 'app') {
	    array_shift($arPath);
        $pathes[] = MUSHROOM_DIR_APP.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $arPath).'.php';
    } else {
        $pathes[] = MUSHROOM_DIR_LIB.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $arPath).'.php';
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
