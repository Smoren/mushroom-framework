<?php

namespace MushroomFramework\Routing;

/**
 * Class Uri
 * @package MushroomFramework\Routing
 */
class Uri {
	protected $uri;
	protected $paramsAdded = false;

	public static function make($addr, ...$data) {
		if(empty($data)) $data = array();
		elseif(is_array($data[0])) $data = $data[0];
//		try {
            $route = Router::findRoute($addr, sizeof($data));
//        } catch(\Throwable $e) {
//		    return $e->getMessage();
//        }
        return new static($route->getUri($data));
	}

	function __construct($uri) {
		$this->uri = $uri;
	}

	public function __toString() {
		return $this->uri;
	}

	public function withParams($params) {
		$this->uri .= '?';
		foreach($params as $key => &$val) {
			$val = urlencode($key).'='.urlencode($val);
		}
		$this->uri .= implode('&', $params);
		return $this->uri;
	}
}