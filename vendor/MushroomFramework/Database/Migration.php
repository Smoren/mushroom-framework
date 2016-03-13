<?php

namespace MushroomFramework\Database;
use \Exception;

/**
 * Migration Class
 * @version 0.1.0
 * @author Smoren <ofigate@gmail.com>
 */
abstract class Migration {
	/**
	 * @var string $tableName Name of the migrations table
	 */
	protected static $tableName = 'migrations';

	/**
	 * Executes the migration's up actions
	 * @return void
	 */
	abstract public function up();

	/**
	 * Executes the migration's down actions
	 * @return void
	 */
	abstract public function down();

	/**
	 * Console application's method for making new migration with name=$name
	 * @throws Exception
	 * @param string $name Name of the migration
	 * @return void
	 */
	public static function make($name) {
		if(!static::installed()) {
			throw new Exception('migrations table is not installed');
		}
		$timestamp = @mktime();
		$datetime = @date('Y_m_d_H_i_s', $timestamp);
		$className = "Migration_{$datetime}_{$name}";
		$fileName = "{$className}.php";
		$migrationClassString = "<?php\n\nuse \MushroomFramework\Database\Migration;\n\nclass $className extends Migration {\n\tpublic function up() {\n\t\t\n\t}\n\tpublic function down() {\n\t\t\n\t}\n}";
		file_put_contents(MUSHROOM_DIR_APP_MIGRATIONS.'/'.$fileName, $migrationClassString);
		QueryBuilder::insert(static::$tableName, array(
			'name' => $name,
			'timestamp' => $timestamp,
			'className' => $className,
			'active' => 0,
		))->exec();
	}

	/**
	 * Console application's method for installing migrations' table
	 * @throws Exception
	 * @return void
	 */
	public static function install() {
		if(static::installed()) {
			throw new Exception('migrations table is already installed');
		}
		QueryBuilder::createTable(static::$tableName, array(
			'id' => 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'name' => 'VARCHAR(100)',
			'timestamp' => 'INT',
			'className' => 'VARCHAR(100)',
			'active' => 'TINYINT',
		))->exec();
	}

	/**
	 * Console application's method for checking if migrations' table installed
	 * @return boolean
	 */
	public static function installed() {
		if(QueryBuilder::showTables(static::$tableName)->exec()->fetch()) {
			return true;
		}
		return false;
	}

	/**
	 * Console application's method for rolling $maxSteps unrolled migrations.
	 * Method rolls all the unrolled migrations if $maxSteps=false or not defined
	 * @throws Exception
	 * @param int $maxSteps
	 * @return void
	 */
	public static function roll($maxSteps=false) {
		if(!static::installed()) {
			throw new Exception('migrations table is not installed');
		}
		$sql = QueryBuilder::select()->from(static::$tableName)->where('active', '=', '0')->orderBy(array('timestamp' => 'ASC'));
		if($maxSteps) $sql->limit(intval($maxSteps));
		$rs = $sql->exec();
		while($migration = $rs->fetch()) {
			$path = MUSHROOM_DIR_APP_MIGRATIONS.'/'.$migration['className'].'.php';
			if(is_file($path)) {
				include($path);
				$instance = new $migration['className']();
				$instance->up();
				QueryBuilder::update(static::$tableName, array('active' => 1))->where('id', '=', $migration['id'])->exec();
				echo "Migration '{$migration['name']}' installed\n";
			} else {
				QueryBuilder::delete(static::$tableName)->where('id', '=', $migration['id'])->exec();
				echo "Migration '{$migration['name']}' not installed (file '$path' not found)\n";
			}
		}
	}

	/**
	 * Console application's method for rolling back $maxSteps unrolled migrations.
	 * Method rolls back 1 unrolled migration if $maxSteps is not defined
	 * @throws Exception
	 * @param int $maxSteps
	 * @return void
	 */
	public static function rollback($maxSteps=1) {
		if(!static::installed()) {
			throw new Exception('migrations table is not installed');
		}
		$sql = QueryBuilder::select()->from(static::$tableName)->where('active', '=', '1')->orderBy(array('timestamp' => 'DESC'));
		if($maxSteps) $sql->limit(intval($maxSteps));
		$rs = $sql->exec();
		while($migration = $rs->fetch()) {
			$path = MUSHROOM_DIR_APP_MIGRATIONS.'/'.$migration['className'].'.php';
			if(is_file($path)) {
				include($path);
				$instance = new $migration['className']();
				$instance->down();
				QueryBuilder::update(static::$tableName, array('active' => 0))->where('id', '=', $migration['id'])->exec();
				echo "Migration '{$migration['name']}' uninstalled\n";
			} else {
				QueryBuilder::delete(static::$tableName)->where('id', '=', $migration['id'])->exec();
				echo "Migration '{$migration['name']}' not uninstalled (file '$path' not found)\n";
			}
		}
	}

	/**
	 * Console application's method for rolling back all unrolled migrations.
	 * @return void
	 */
	public static function reset() {
		static::rollback(0);
	}

	/**
	 * Console application's method for rolling back all unrolled migrations.
	 * @param string $tableName Database table name
	 * @return Table
	 */
	protected function table($tableName) {
		return Table::get($tableName);
	}

	/**
	 * Console application's method for rolling back all unrolled migrations.
	 * @param string $tableName Database table name
	 * @param array $fields Table fields' array
	 * @return Table
	 */
	protected function createTable($tableName, array $fields) {
		return Table::create($tableName, $fields);
	}
}