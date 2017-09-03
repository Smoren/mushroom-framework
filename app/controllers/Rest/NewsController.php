<?php

namespace Rest;
use \MushroomFramework\Pattern\Uuid;
use MushroomFramework\Extensions\Controllers\RestController;

class NewsController extends RestController {
 	protected static $modelName = 'News';
	protected static $filterFields = array('name', 'text');
	protected static $orderFields = array();
}