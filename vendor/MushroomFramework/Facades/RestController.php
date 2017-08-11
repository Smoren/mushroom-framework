<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Routing\Controller;
use \Exception;

abstract class RestController extends Controller {
	protected static $modelName;

	function __call($methodName, $args=array()) {
		if(is_callable(array($this, $methodName))) {
			return call_user_func_array(array($this, $methodName), $args);
		} else {
			throw new Exception('In controller '.get_called_class().' method '.$methodName.' not found!');
		}
	}

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

	// TODO catch Exceptions on not Response object at main and make 404

	public function list() {
		$modelName = static::$modelName;
		$list = $modelName::select()->getArrayList();
		return Response::json($list);
	}

	public function create() {
		// TODO: научиться обрабатывать валидацию
		$modelName = static::$modelName;
		$item = new $modelName(Request::json());
		$item->save();
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
		// TODO: научиться обрабатывать валидацию
		$modelName = static::$modelName;
		$item = $modelName::find($id);
		if(!$item) {
			Response::status(404);
			return Response::json(array('error' => 'item not found'));
		}
		$item->set(Request::json());
		$item->save();
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