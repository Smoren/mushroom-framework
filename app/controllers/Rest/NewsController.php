<?php

namespace Rest;

class NewsController extends \RestController {
 	protected static $modelName = 'News';
	protected static $filterFields = array(
		'name' => array('~'), 
		'text' => array('=', '~'),
	);
	protected static $filterOperators = array('AND', 'OR');
	protected static $orderFields = array(
		'name' => array('ASC', 'DESC'),
	);
}