<?php

namespace MushroomFramework\View;

/**
 * Class Html
 * @package MushroomFramework\View
 */
class Html {
	public static function link($addr, $name, $htmlAttrs=false) {
		if(!is_array($htmlAttrs)) $htmlAttrs = array();

		$htmlAttrs['href'] = $addr;

		return static::tag('a', $name, $htmlAttrs);
	}

	public static function tag($tagName, $innerHtml, $attrs=false) {
		$attrs = is_array($attrs) ? $attrs : array();
		foreach($attrs as $key => &$val) {
			$key = addslashes($key);
			$val = addslashes($val);
			$val = "$key=\"$val\"";
		}
		$attrs = implode(' ', $attrs);
		return "<$tagName $attrs>$innerHtml</$tagName>";
	}

	public static function table($thead, $tbody, $tfoot, $attrs=false) {

	}
}