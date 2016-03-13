<?php

class UserTest extends PHPUnit_Framework_TestCase {
	protected $username = 'test_user1@test.ru';
	protected $password = '12345';

	public function testNotExist() {
		$user = $this->getUser();
		$this->assertFalse($user, 'пользователь существует, а НЕ должен');
	}

	/**
     * @depends testNotExist
     */
	public function testAdd() {
		$user = $this->addUser();
		$this->assertNotFalse($user, 'не удалось добавить пользователя: '.User::error());
	}

	/**
     * @depends testAdd
     */
	public function testDublicate() {
		$user = $this->addUser();
		$this->assertFalse($user, 'удалось продублировать пользователя');
	}

	/**
     * @depends testAdd
     */
	public function testExist() {
		$user = $this->getUser();
		$this->assertNotFalse($user, 'пользователь НЕ существует, а должен');
	}

	/**
     * @depends testAdd
     */
	public function testLogin() {
		$user = User::login($this->username, $this->password, false, true);
		$this->assertNotFalse($user, 'не удалось авторизоваться');
	}

	/**
     * @depends testLogin
     */
	public function testLoginFailure() {
		$user = User::login($this->username, '', false, true);
		$this->assertFalse($user, 'удалось авторизоваться с пустым паролем');
		$user = User::login($this->username, '123', false, true);
		$this->assertFalse($user, 'удалось авторизоваться с неправильным паролем');
		// $this->fail('message');
	}

	/**
     * @depends testLoginFailure
     */
	public function testRemove() {
		$user = $this->getUser();
		$user->remove();
		$this->testNotExist();
	}

	public function addUser() {
		$data = array('email' => $this->username, 'password' => $this->password, 'name' => 'test');
		return User::register($data);
	}

	public function getUser() {
		return User::findBy('email', $this->username)->getFirst();
	}
}