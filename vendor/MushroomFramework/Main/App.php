<?php

namespace MushroomFramework\Main;
use \MushroomFramework\Routing\Router;
use \MushroomFramework\Pattern\Singleton;
use \MushroomFramework\Database\DatabaseManager;
use \MushroomFramework\InputOutput\Event;
use \MushroomFramework\Facades\QueryBuilder;
use \Exception;
use \Closure;

class App extends Singleton {
	public static $instance;
	protected $database;
	protected $router;
	protected $config;
	protected $properties = array();

	public static function start() {
		try {
			$app = static::gi();

			// запускаем init-файл приложения
			Event::trigger('onBeforeInitIncluded', $app);
			require_once(MUSHROOM_DIR_APP.'/init.php');
			Event::trigger('onAfterInitIncluded', $app);
			
			// запускаем роутер и передаем ему управление
			$app->router = new Router();
			Event::trigger('onBeforeRouting', $app);
			$response = $app->router->dispatch();
			Event::trigger('onAfterRouting', $app);

			Event::trigger('onBeforeResponse', $app);
			echo $response->make();
			Event::trigger('onAfterResponse', $app);
		} catch(Exception $e) {
			echo $e->getMessage();
			echo '<pre>'; print_r($e); echo '</pre>';;
		}
	}

	public function init() {
		// подключаем конфигурацию
		$this->config = require(MUSHROOM_DIR_APP_CONFIG.'/config.php');

		// настраиваем алиасы классов
		foreach($this->config['classAliases'] as $serviceName => $className) {
			class_alias($className, $serviceName);
		}

		// подключаем СУБД
		Event::trigger('onBeforeDatabaseInit', $app);
		if($this->config['database'] && $this->config['database']['type']) {
			$this->database = DatabaseManager::get($this->config['database']);
			QueryBuilder::setDatabaseType($this->config['database']['type']);
			QueryBuilder::setEncoding($this->database->getEncoding(), $this->database->getCollate());
			QueryBuilder::setDatabaseManager($this->database);
		}
		Event::trigger('onAfterDatabaseInit', $app);
	}

	// возвращает текущий роутер
	public function getRouter() {
		return $this->router;
	}

	// возвращает конигурацию
	public function getConfig() {
		return $this->config;
	}

	// возвращает опцию конфигурации по имени
	public function getConfigOption($name=false) {
		if($name && isset($this->config[$name])) {
			return $this->config[$name];
		}
		return false;
	}

	// возвращает объект для работы с БД
	public function getDbInterface() {
		return $this->database;
	}

	public function property($name, $value=null) {
		if($value === null) return $this->getProperty($name);
		else return $this->setProperty($name, $value);
	}

	public function setProperty($name, $value) {
		$this->properties[$name] = $value;
		return $this;
	}

	public function getProperty($name) {
		if(!isset($this->properties[$name])) {
			throw new Exception("property '$name' doesn't exist");
		}
		return $this->properties[$name];
	}
}