<?php

namespace MushroomFramework\View;

// класс представления
class View {
	protected static $scripts = array(); // массив путей к скриптам
	protected static $styles = array(); // массив путей к стилям
	protected static $meta = array(); // массив мета-тегов
	protected $dir = ''; // каталог, в котором находятся views относительно /app/views
	protected $path; // путь к шаблону, без php
	protected $args; // аргументы, переданные view
	protected $sections; // секции для вставки в родительский элемент
	protected $html; // строка html шаблона
	protected $parent = false; // родительский view
	protected $currentSectionName = false; // имя текущей секции

	function __construct($path='', $dir='') {
		if($path) $this->init($path, $dir);
	}

	public function init($path, $dir='') {
		$arDir = explode('/', "{$dir}/{$path}");
		array_pop($arDir);
		$this->dir = implode('/', $arDir);
		$this->path = MUSHROOM_DIR_APP_VIEWS."{$dir}/{$path}.php";
		if(!is_file($this->path)) {
			throw new Exceptions\ViewException("view '{$this->path}' is not found");
		}
	}

	// подключает и выполняет view
	public function make($args=false, $sections=false) {
		if(!is_array($this->args)) {
			$this->args = is_array($args) ? $args : array();
		}
		$this->sections = is_array($sections) ? $sections : array();
		extract($this->args, EXTR_PREFIX_INVALID, 'invalid_');

		ob_start();
		include($this->path);
		$this->html = ob_get_contents();
		ob_end_clean();

		if($this->parent) {
			$this->sections[''] = $this->html;
			return $this->parent->make($this->args, $this->sections);
		} else {
			return $this->html;
		}
	}

	public function setArgs($args=false) {
		$this->args = is_array($args) ? $args : array();
	}

	// подключает родительский шаблон
	public function parent($path) {
		$this->parent = new static($path, $this->dir);
	}

	// вставляет секцию
	public function place($name='') {
		if(!isset($this->sections[$name])) {
			throw new Exceptions\ViewException("place: section '$name' not found");
		}
		return $this->sections[$name];
	}

	// подключает view
	public function insert($path) {
		$view = new static($path, $this->dir);
		return $view->make($this->args, $this->sections);
	}

	// начинает собирать буфер вывода для контента секции
	public function section($name) {
		$this->currentSectionName = $name;
		ob_start();
	}

	// заканчивает собирать буфер вывода для контента секции
	public function end() {
		if(!isset($this->currentSectionName)) {
			throw new Exceptions\ViewException("no section initiated");
		}
		$this->sections[$this->currentSectionName] = ob_get_contents();
		ob_end_clean();
	}

	public function styles() {
		$res = '';
		foreach(static::$styles as $src) {
			$res .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$src}\" />\n";
		}
		return $res;
	}

	public function scripts() {
		$res = '';
		foreach(static::$scripts as $src) {
			$res .= "<script type=\"text/javascript\" src=\"$src\"></script>\n";
		}
		return $res;
	}

	public function meta() {
		$res = '';
		foreach(static::$meta as $name => $content) {
			$res .= "<meta name=\"{$name}\" content=\"{$content}\" />\n";
		}
		return $res;
	}

	public function addStyle($src) {
		static::$styles[] = $src;
	}

	public function addScript($src) {
		static::$scripts[] = $src;
	}

	public function addMeta($name, $content) {
		static::$meta[$name] = $content;
	}
}