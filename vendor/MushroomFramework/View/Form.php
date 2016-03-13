<?php

namespace MushroomFramework\View;

class Form {
	public static function start($action, $method, $enctype, $attrs) {
		$attrs['action'] = $action;
		$attrs['method'] = $method;
		$attrs['enctype'] = $enctype ? $enctype : 'application/x-www-form-urlencoded';
		return "<form".static::getAttrsString($attrs).">";
	}

	public static function end() {
		return "</form>";
	}

	public static function submit($name='', $value='', $attrs='') {
		$attrs['type'] = 'submit';
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		$res = "<input".static::getAttrsString($attrs)." />";
		return $res;
	}

	public static function button($name='', $value='', $attrs='') {
		// $attrs['type'] = 'submit';
		$attrs['name'] = $name;
		$res = "<button".static::getAttrsString($attrs).">{$value}</button>";
		return $res;
	}

	public static function checkbox($name='', $value='', $attrs='', $title='') {
		$res = "";
		$value = $value ? $value : 'On';
		if(!(($val = static::getValueFromRequest($name, true)) === false)) {
			if(is_array($val)) {
				if(in_array($value, $val)) {
					$attrs['checked'] = 'checked';
				}
			} elseif($val == $value) {
				$attrs['checked'] = 'checked';
			}
		} elseif($attrs['checked']) {
			$attrs['checked'] = 'checked';
		} else {
			unset($attrs['checked']);
		}
		$attrs['type'] = 'checkbox';
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		if($title) {
			$res .= "<label>";
		}
		$res .= "<input".static::getAttrsString($attrs)." />";
		if($title) {
			$res .= " {$title}</label>";
		}
		return $res;
	}

	public static function radio($name='', $value='', $attrs='', $title='') {
		$res = "";
		if(!(($val = static::getValueFromRequest($name)) === false)) {
			if($val == $value) {
				$attrs['checked'] = 'checked';
			}
		} elseif($attrs['checked']) {
			$attrs['checked'] = 'checked';
		} else {
			unset($attrs['checked']);
		}
		$attrs['type'] = 'radio';
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		if($title) {
			$res .= "<label>";
		}
		$res .= "<input".static::getAttrsString($attrs)." />";
		if($title) {
			$res .= " {$title}</label>";
		}
		return $res;
	}

	public static function text($name='', $value='', $attrs='') {
		if($value === false) {
			$value = '';
		} elseif(!(($val = static::getValueFromRequest($name)) === false)) {
			$value = $val;
		}
		$attrs['type'] = 'text';
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		$res = "<input".static::getAttrsString($attrs)." />";
		return $res;
	}

	public static function password($name='', $value='', $attrs='') {
		if($value === false) {
			$value = '';
		} elseif(!(($val = static::getValueFromRequest($name)) === false)) {
			$value = $val;
		}
		$attrs['type'] = 'password';
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		$res = "<input".static::getAttrsString($attrs)." />";
		return $res;
	}

	public static function hidden($name='', $value='', $attrs='') {
		if($value === false) {
			$value = '';
		} elseif(!(($val = static::getValueFromRequest($name)) === false)) {
			$value = $val;
		}
		$attrs['type'] = 'hidden';
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		$res = "<input".static::getAttrsString($attrs)." />";
		return $res;
	}

	public static function file($name='', $attrs='') {
		$attrs['type'] = 'file';
		$attrs['name'] = $name;
		$res = "<input".static::getAttrsString($attrs)." />";
		return $res;
	}

	public static function textarea($name='', $value='', $attrs='') {
		if($val === false) {
			$val = '';
		} elseif(!(($val = static::getValueFromRequest($name)) === false)) {
			$value = $val;
		}
		$attrs['type'] = 'text';
		$attrs['name'] = $name;
		$value = htmlspecialchars($value);
		$res = "<textarea".static::getAttrsString($attrs).">{$value}</textarea>";
		return $res;
	}

	public static function tinymce($name='', $value='', $attrs='') {
		$className = 'tinymce-'.rand(100000, 999999);
		if(!is_array($attrs)) {
			$attrs = array(
				'class' => $className,
			);
		} elseif(!$attrs['class']) {
			$attrs['class'] = $className;
		} else {
			$attrs['class'] .= " $className";
		}
		$res = '<script type="text/javascript">
			tinymce.init({
			    selector: ".'.$className.'",
			    plugins: [
			        "advlist autolink lists link image charmap print preview anchor",
			        "searchreplace visualblocks code fullscreen",
			        "insertdatetime media table contextmenu paste"
			    ],
			    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
			});
			</script>';
		return static::textarea($name, $value, $attrs).$res;
	}

	public static function getAttrsString($attrs) {
		foreach($attrs as $attrName => $attrVal) {
			$attrName = htmlspecialchars($attrName);
			$attrVal = htmlspecialchars($attrVal);
			$res .= " {$attrName}=\"$attrVal\"";
		}
		return $res;
	}

	public static function getValueFromRequest($name, $debug=false) {
		if(!preg_match_all('/\[([^\]]*)\]/', $name, $matches)) {
			return isset($_REQUEST[$name]) ? $_REQUEST[$name] : false;
		} else {
			global $app;
			preg_match_all('/^([^\[]*)\[/', $name, $match);
			$path = array_merge($match[1], $matches[1]);
			$res = $_REQUEST;
			foreach($path as $val) {
				if($val && isset($res[$val])) {
					$res = $res[$val];
				} elseif($val) {
					return false;
				}
			}
			return $res;
		}
	}
}