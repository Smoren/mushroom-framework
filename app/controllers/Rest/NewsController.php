<?php

namespace Rest;

class NewsController extends \RestController {
 	protected static $modelName = 'News';
	protected static $filterFields = array('name', 'text');
	protected static $orderFields = array();
}