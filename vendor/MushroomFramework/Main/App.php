<?php

namespace MushroomFramework\Main;
use \MushroomFramework\Routing\Router;
use \MushroomFramework\Pattern\Singleton;
use \MushroomFramework\ORMushroom\DatabaseSession;
use \MushroomFramework\InputOutput\Event;
use \Exception;
use \Error;
use \Closure;

class App extends Singleton {
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
			static::showError($e);
		} catch(Error $e) {
			static::showError($e);
		}
	}

	public static function showError($e) {
	    ob_end_clean();
		http_response_code(500);
		if(static::gi()->config['debug']) {
			echo $e->getMessage();
			echo '<pre>'; print_r($e); echo '</pre>';
			die();
		} else {
			error_log($e->getMessage());
			// TODO: выводить стандартную страницу 500
		}
	}

	public function init() {
		try {
			// подключаем конфигурацию
			$this->config = require(MUSHROOM_DIR_APP_CONFIG.'/config.php');

			// настраиваем алиасы классов
			foreach($this->config['classAliases'] as $serviceName => $className) {
				class_alias($className, $serviceName);
			}

			// подключаем СУБД
			Event::trigger('onBeforeDatabaseInit', $app);
			if($this->config['database'] && $this->config['database']['type']) {
				$this->database = new DatabaseSession($this->config['database'], true);
			}
			Event::trigger('onAfterDatabaseInit', $app);
		} catch(Exception $e) {
			static::showError($e);
		} catch(Error $e) {
			static::showError($e);
		}
	}

	// возвращает текущий роутер
	public function getRouter() {
		return $this->router;
	}

	// возвращает конфигурацию
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

	public function getProperty($name, $default=null) {
		if(!isset($this->properties[$name])) {
			if($default === null) {
				throw new Exception("property '$name' doesn't exist");
			}
			return $default;
		}
		return $this->properties[$name];
	}

	public function initModules() {
        $dh = opendir(MUSHROOM_DIR_APP_MODULES);
        while($fname = readdir($dh)) {
            $filePath = MUSHROOM_DIR_APP_MODULES . DIRECTORY_SEPARATOR . $fname;
            $initFilePath = $filePath . DIRECTORY_SEPARATOR . 'init.php';
            if (is_dir($filePath) && is_file($initFilePath)) {
                include_once($initFilePath);
            }
        }
    }

}