<?php

namespace MushroomFramework\Extensions\Controllers;
use \MushroomFramework\Facades\Router;
use \MushroomFramework\Facades\Response;
use \MushroomFramework\Facades\Request;
use \MushroomFramework\Routing\Controller;
use \MushroomFramework\Database\Exceptions\ValidatorException;
use \Exception;

abstract class RestController extends Controller {
	protected static $modelName;
	protected static $filterFields = array();
	protected static $orderFields = array();

	public function collection() {
		switch(Router::getMethod()) {
			case 'GET':
				return $this->list();
				break;
			case 'POST':
				return $this->create();
				break;
			default:
				Response::status(405);
				return Response::json(array('error' => 'method not allowed'));
		}
	}

	public function item($id) {
		switch(Router::getMethod()) {
			case 'GET':
				return $this->detail($id);
				break;
			case 'PUT':
				return $this->update($id);
				break;
			case 'DELETE':
				return $this->remove($id);
				break;
			default:
				Response::status(405);
				return Response::json(array('error' => 'method not allowed'));
		}
	}

	public function list() {
		$modelName = static::$modelName;
		$query = $modelName::select();

		$method = 'where';
		foreach(static::$filterFields as $fieldName) {
			$fieldValue = Request::get($fieldName, false);
			if($fieldValue !== false) {
				$query->$method($fieldName, '=', $fieldValue);
				$method = 'andWhere';
			}
		}

		$list = $query->getArrayList();
		return Response::json($list);
	}

	public function create() {
		$modelName = static::$modelName;
		$item = new $modelName(Request::json());
		try {
			$item->save();
		} catch(ValidatorException $e) {
			Response::status(422);
			return Response::json(array(
				'error' => 'validation error',
				'errorFields' => $e->getErrorFields(),
			));
		}
		return Response::json($item->asArray());
	}

	public function detail($id) {
		$modelName = static::$modelName;
		$item = $modelName::find($id);
		if(!$item) {
			Response::status(404);
			return Response::json(array('error' => 'item not found'));
		}
		return Response::json($item->asArray());
	}

	public function update($id) {
		$modelName = static::$modelName;
		$item = $modelName::find($id);
		if(!$item) {
			Response::status(404);
			return Response::json(array('error' => 'item not found'));
		}
		$item->set(Request::json());
		try {
			$item->save();
		} catch(ValidatorException $e) {
			Response::status(422);
			return Response::json(array(
				'error' => 'validation error',
				'errorFields' => $e->getErrorFields(),
			));
		}
		return Response::json($item->asArray());
	}

	public function remove($id) {
		$modelName = static::$modelName;
		$item = $modelName::find($id);
		if(!$item) {
			Response::status(404);
			return Response::json(array('error' => 'item not found'));
		}
		$item->remove();
		return Response::json(null);
	}
}