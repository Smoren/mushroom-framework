<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class Form
 * @package MushroomFramework\Facades
 * @method start($action, $method, $enctype, $attrs) static
 * @method end() static
 * @method submit($name='', $value='', $attrs=null) static
 * @method button($name='', $value='', $attrs=null) static
 * @method checkbox($name='', $value='', $attrs=null, $title='') static
 * @method radio($name='', $value='', $attrs=null, $title='') static
 * @method select($name='', $value='', $items=null, $attrs='') static
 * @method text($name='', $value='', $attrs=null) static
 * @method number($name='', $value='', $attrs=null) static
 * @method password($name='', $value='', $attrs=null) static
 * @method hidden($name='', $value='', $attrs=null) static
 * @method file($name='', $attrs=null) static
 * @method textarea($name='', $value='', $attrs=null) static
 * @method tinymce($name='', $value='', $attrs=null) static
 * @method getAttrsString($attrs) static
 * @method getValueFromRequest($name, $debug=false) static
 */
class Form extends Facade {
	protected static $locClassName = '\\MushroomFramework\\View\\Form';
}