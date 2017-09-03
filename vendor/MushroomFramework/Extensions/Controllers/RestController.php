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
	protected static $messageMethodNotAllowed = 'method not allowed';
	protected static $messageItemNotFound = 'item not found';
	protected static $messageValidationError = 'validation error';

	public function collection() {
		switch(Router::getMethod()) {
			case 'GET':
				return $this->list(Request::get());
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

	// TODO идея реализации фильтра и сортировки: '~name' => array('LIKE', 'name'), 'id' => array('=')
	// массивы $filterFields и $orderFields приводятся в единообразную структуру в конструкторе RestController
	// идти по выражению со скобками и сразу в процессе транслировать в QB-запрос
	// посимвольно проверяем, не экранирован ли очередной символб но только для получения значений
	// экранировать необходимо символ [']
	// filter: id>10&(title~'my~name%'|text~'my~name%')
	// на тогда требуется валидация запроса: введем счетчик скобок, должен быть = 0 на выходе
	// если хотя бы один из параметров не найден, возвращаем ошибку (все норм, ибо в отдельной переменной)
	protected function list($params=array(), $filterFields=null, $orderFields=null) {
		if(!$filterFields) $filterFields = static::$filterFields;
		if(!$orderFields) $orderFields = static::$orderFields;

		$modelName = static::$modelName;
		$query = $modelName::select();

		$method = 'where';
		foreach($params as $fieldName => $fieldValue) {
			if(in_array($fieldName, $filterFields)) {
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