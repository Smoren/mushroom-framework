<?php

namespace MushroomFramework\Models;
use MushroomFramework\Database\Model;
use MushroomFramework\Facades\Cookie;
use MushroomFramework\Facades\Session;

abstract class User extends Model {
	const ROW_ID = 'id';
	const ROW_LOGIN = 'email';
	const ROW_PASSWORD = 'password';
	const ROW_HASH_AUTH = 'hash_auth';
	const ROW_HASH_RESTORE = 'hash_restore';
	const COOKIE_ID = 'userId';
	const COOKIE_HASH_AUTH = 'userHashAuth';
	const SESSION_KEY = 'user';
	const AUTH_TIME = 3600;
	protected static $error = false;

	public static function login($login=null, $password=null, $remember=false, $checkOnly=false) {
		static::$error = false;
		$password = static::encryptPassword($password);
		$user = static::select()->where(static::ROW_LOGIN, '=', $login)->andWhere(static::ROW_PASSWORD, '=', $password)->getFirst();
		if(!$user) {
			static::$error = 'INCORRECT_LOGIN_OR_PASSWORD';
			return false;
		}
		if(!$checkOnly) $user->auth($remember);
		return $user;
	}

	public static function authorized() {
		static::$error = false;

		$cookieUser = Cookie::only(static::COOKIE_ID, static::COOKIE_HASH_AUTH);
		$cookieUserId = $cookieUser[static::COOKIE_ID];
		$cookieUserHashAuth = $cookieUser[static::COOKIE_HASH_AUTH];
		if(!$cookieUserId || !$cookieUserHashAuth) {
			static::$error = 'NO_AUTH_COOKIE';
			return false;
		}

		$sessionUser = Session::get(static::SESSION_KEY, false);
		$sessionUserId = isset($sessionUser[static::ROW_ID]) ? $sessionUser[static::ROW_ID] : false;
		$sessionUserHashAuth = isset($sessionUser[static::ROW_HASH_AUTH]) ? $sessionUser[static::ROW_HASH_AUTH] : false;
		if($sessionUserId && $sessionUserId === $cookieUserId && $sessionUserHashAuth == $cookieUserHashAuth) {
			$user = new static($sessionUser);
			$user->exists = true;
			return $user;
		}

		$user = static::select()->where('id', '=', $cookieUserId)->andWhere(static::ROW_HASH_AUTH, '=', $cookieUserHashAuth)->getFirst();
		if(!$user) {
			static::$error = 'INCORRECT_AUTH_COOKIE';
			return false;
		}
		$user->auth(true);
		return $user;
	}

	public static function register($data) {
		static::$error = false;

		$user = new static();
		if(!$user->set($data)->success()) {
			static::$error = 'REGISTER_VALIDATION_FALSE';
			return false;
		}

		$login = isset($data[static::ROW_LOGIN]) ? $data[static::ROW_LOGIN] : '';
		if(!$login) {
			static::$error = 'REGISTER_NO_LOGIN';
			return false;
		}

		$found = static::findBy(static::ROW_LOGIN, $login)->getFirst();
		if($found) {
			static::$error = 'REGISTER_DUBLICATE_USER';
			return false;
		}
		
		$password = isset($data[static::ROW_PASSWORD]) ? $data[static::ROW_PASSWORD] : '';
		if(!$password) {
			static::$error = 'REGISTER_NO_PASSWORD';
			return false;
		}

		$user->{static::ROW_HASH_RESTORE} = static::makeHash();
		$user->save();
		return $user;
	}

	public static function encryptPassword($str) {
		$res = md5(md5($str));
		$res = md5(substr($res, 3, 10).$res);
		return $res;
	}

	public static function makeHash() {
		return md5(md5(rand(10000000, 99999999)));
	}

	public static function logout() {
		Cookie::set(static::COOKIE_ID);
		Cookie::set(static::COOKIE_HASH_AUTH);
		Session::set(static::SESSION_KEY, false);
		return $this;
	}
	
	public function auth($remember) {
		$time = $remember ? null : static::AUTH_TIME;
		$rowHashAuth = $this->{static::ROW_HASH_AUTH} = static::makeHash();
		$this->save();
		Cookie::set(static::COOKIE_ID, $this->id, $time);
		Cookie::set(static::COOKIE_HASH_AUTH, $rowHashAuth, $time);
		Session::set(static::SESSION_KEY, $this->asArray());
		return $this;
	}

	public function set(array $data, $validate=true) {
		foreach($data as $key => &$val) {
			if(in_array($key, array(static::ROW_HASH_AUTH, static::ROW_HASH_RESTORE))) {
				unset($data[$key]);
			} elseif($key == static::ROW_PASSWORD) {
				$val = static::encryptPassword($val);
			}
		}
		return parent::set($data, $validate);
	}

	public function error() {
		return static::$error;
	}
}
