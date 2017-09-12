<?php

namespace MushroomFramework\ORMushroom;
use \MushroomFramework\ORMushroom\Base;

/**
 * QueryBuilder Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
class QueryBuilder extends Base\SessionDecorator {
	public function __toString() {
		return $this->obj->__toString();
	}

	public function exec() {
		return parent::exec($this->_databaseSession);
	}
}
