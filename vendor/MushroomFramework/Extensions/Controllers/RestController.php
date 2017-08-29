<?php

namespace MushroomFramework\Extensions\Controllers;
use \MushroomFramework\Facades\Router;
use \MushroomFramework\Facades\Response;
use \MushroomFramework\Facades\Request;
use \MushroomFramework\Routing\Controller;
use \MushroomFramework\Database\Exceptions\ValidatorException;

abstract class RestController extends Controller {
	protected static $modelName;
	protected static $filterFields = array();
	protected static $orderFields = array();
	protected static $messageMethodNotaAllowed = 'method not allowed';
	protected static $messageItemNotFound = 'item not found';
	protected static $messageValidationError = 'validation error';

	// TODO передавать в переопределяемые методы параметры
	public function collection() {
		switch(Router::getMethod()) {
			case 'GET':
				return $this->list(static::$filterFields);
				break;
			case 'POST':
				return $this->create(Request::json());
				break;
			default:
				Response::status(405);
				return Response::json(array('error' => static::$messageMethodNotaAllowed));
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
				return Response::json(array('error' => static::$messageMethodNotaAllowed));
		}
	}

	protected function list($filterFields=array()) {
		$modelName = static::$modelName;
		$query = $modelName::select();

		$method = 'where';
		foreach($filterFields as $fieldName) {
			$fieldValue = Request::get($fieldName, false);
			if($fieldValue !== false) {
				$query->$method($fieldName, '=', $fieldValue);
				$method = 'andWhere';
			}
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