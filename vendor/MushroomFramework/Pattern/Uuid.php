<?php

namespace MushroomFramework\Pattern;

class Uuid {
	protected $value;

	function __construct($uuid) {
		$this->value = $uuid;
	}

	function __toString() {
		return $this->value;
	}

	public static function generate() {
		if(extension_loaded('uuid')) {
			if(function_exists('uuid_export') && uuid_create($ctx) == UUID_RC_OK && uuid_make($ctx, UUID_MAKE_V4) == UUID_RC_OK && uuid_export($ctx, UUID_FMT_STR, $str) == UUID_RC_OK) {
				// UUID extension from http://www.ossp.org/pkg/lib/uuid/
				$uuid = $str;
				uuid_destroy($ctx);
			} else {
				// UUID extension from http://pecl.php.net/package/uuid
				$uuid = uuid_create();
			}
		} else {
			list($time_mid, $time_low) = explode(' ', microtime());
			$time_low = (int)$time_low;
			$time_mid = (int)substr($time_mid, 2) & 0xffff;
			$time_high = mt_rand(0, 0xfff) | 0x4000;
			$clock = mt_rand(0, 0x3fff) | 0x8000;
			$node_low = function_exists('zend_thread_id') ? zend_thread_id() : getmypid();
			$node_high = isset($_SERVER['SERVER_ADDR']) ? ip2long($_SERVER['SERVER_ADDR']) : crc32(php_uname());
			$node = bin2hex(pack('nN', $node_low, $node_high));
			$uuid = sprintf('%08x-%04x-%04x-%04x-%s', $time_low, $time_mid, $time_high, $clock, $node);
		}

		return new static($uuid);
	}

	public static function fromBin($bin) {
		$hex = bin2hex($bin);
		$result = substr($hex, 0, 8)
			.'-'.substr($hex, 8, 4)
			.'-'.substr($hex, 12, 4)
			.'-'.substr($hex, 16, 4)
			.'-'.substr($hex, 20, 12);
		return new static($result);
	}

	public function toBin() {
		return hex2bin(str_replace('-', '', $this->value));
	}
}