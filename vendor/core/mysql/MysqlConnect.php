<?php

namespace core\mysql;


/**
 * Класс для создания соединения с базой данных
 *
 * Class MysqlConnect
 * @package core\mysql
 */

class MysqlConnect extends SelectSqlBuilder{

    /** @var \PDO */
    protected $pdo;

    /** @var \PDOStatement */
    protected $stmt;

	/**
	 * Singleton methods
	 * 
	 * @param array $config['db']
	 */
	protected function connect($config){

        if (null === $this->pdo) {

            $dsn = $config['driver']
                . ':host=' . $config['host']
                . ';port=' . $config['port']
                . ';dbname=' . $config['dbname']
                . ';charset=' . $config['charset'];

            $this->pdo = new \PDO($dsn, $config['user'], $config['password'], $config['options']);
		}
	}
}
