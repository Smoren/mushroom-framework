<?php

namespace MushroomFramework\Extensions\Controllers;
use \MushroomFramework\Facades\Router;
use \MushroomFramework\Facades\Response;
use \MushroomFramework\Facades\Request;
use \MushroomFramework\Routing\Controller;
use \MushroomFramework\Database\Exceptions\ValidatorException;
use MushroomFramework\Database\Exceptions\QueryBuilderException;

abstract class RestController extends Controller {
	protected static $modelName;
	protected static $filterFields = array();
	protected static $filterOperators = array();
	protected static $orderFields = array();
	protected static $messageMethodNotAllowed = 'method not allowed';
	protected static $messageItemNotFound = 'item not found';
	protected static $messageValidationError = 'validation error';

	public function collection() {
		switch(Router::getMethod()) {
			case 'GET':
				$params = Request::get();
				if(!isset($params['filter'])) $params['filter'] = array();
				if(!isset($params['order'])) $params['order'] = array();
				return $this->list($params, static::$filterFields, static::$filterOperators, static::$orderFields);
				break;
			case 'POST':
				return $this->create(Request::json());
				break;
			default:
				Response::status(405);
				return Response::json(array('error' => static::$messageMethodNotAllowed));
		}
	}

	public function item($id) {
		switch(Router::getMethod()) {
			case 'GET':
				return $this->detail($id);
				break;
			case 'PUT':
				return $this->update($id, Request::json());
				break;
			case 'DELETE':
				return $this->remove($id);
				break;
			default:
				Response::status(405);
				return Response::json(array('error' => static::$messageMethodNotAllowed));
		}
	}

	// filter: id>10&(title~'my~name%'|text~'my~name%')
	// order: name:asc,text:desc
	protected function list($params=array('filter' => array(), 'order' => array()), $filterFields=array(), $filterOperators=array(), $orderFields=array()) {
		$modelName = static::$modelName;

		try {
			$query = $modelName::select()
				->parseFilter($params['filter'], $filterFields, $filterOperators)
				->parseOrder($params['order'], $orderFields);
		} catch(QueryBuilderException $e) {
			Response::status(422);
			return Response::json(array('error' => $e->getMessage()));
		}

		$list = $query->getArrayList();
		return Response::json($list);
	}

	protected function create($data=array()) {
		$modelName = static::$modelName;
		$item = new $modelName($data);
		try {
			$item->save();
		} catch(ValidatorException $e) {
			Response::status(422);
			return Response::json(array(
				'error' => static::$messageValidationError,
				'errorFields' => $e->getErrorFields(),
			));
		}
		Response::status(201);
		return Response::json($item->asArray());
	}

	protected function detail($id) {
		$modelName = static::$modelName;
		$item = $modelName::find($id);
		if(!$item) {
			Response::status(404);
			return Response::json(array('error' => static::$messageItemNotFound));
		}
		return Response::json($item->asArray());
	}

	protected function update($id, $data=array()) {
		$modelName = static::$modelName;
		$item = $modelName::find($id);
		if(!$item) {
			Response::status(404);
			return Response::json(array('error' => static::$messageItemNotFound));
		}
		$item->set($data);
		try {
			$item->save();
		} catch(ValidatorException $e) {
			Response::status(422);
			return Response::json(array(
				'error' => static::$messageValidationError,
				'errorFields' => $e->getErrorFields(),
			));
		}
		return Response::json($item->asArray());
	}

	protected function remove($id) {
		$modelName = static::$modelName;
		$item = $modelName::find($id);
		if(!$item) {
			Response::status(404);
			return Response::json(array('error' => static::$messageItemNotFound));
		}
		$item->remove();
		return Response::json(null);
	}
}