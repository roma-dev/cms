<?php

namespace core\mysql;


/**
 * Класс для создания соединения с базой данных
 *
 * Class MysqlConnect
 * @package core\mysql
 */

class MysqlConnect{

    /** @var object PDO */
    private $pdo;


	/**
	 * Singleton methods
	 * 
	 * @param array $config $config['db']
	 * @return object \PDO
	 */
	protected function connect($config){

        if (null === $this->pdo) {

            $dsn = $config['driver']
                . ':host=' . $config['host']
                . ';port=' . $config['port']
                . ';dbname=' . $config['dbname']
                . ';charset=' . $config['charset'];

            $this->connect = new \PDO($dsn, $config['user'], $config['password'], $config['options']);
		}
	}
}
