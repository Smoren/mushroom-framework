<?php

use MushroomFramework\Pattern\Uuid;

class RestNewsController extends RestController {
 	protected static $modelName = 'News';
	protected static $filterFields = array('name', 'text');
	protected static $orderFields = array();
}