<?php

namespace MushroomFramework\Facades;
use \MushroomFramework\Pattern\Facade;

/**
 * Class Html
 * @package MushroomFramework\Facades
 * @method tag($tagName, $innerHtml, $attrs=false) static
 * @method table($thead, $tbody, $tfoot, $attrs=false) static
 */
class Html extends Facade {
	protected static $locClassName = '\\MushroomFramework\\View\\Html';

	public static function link($addr, $name, $data=false, $params=false, $htmlAttrs=false) {
		$addr = Uri::make($addr, $data);
		if(is_array($params) && sizeof($params)) {
			$addr = $addr->withParams($params);
		}
		return static::call('link', array($addr, $name, $htmlAttrs));
	}
}