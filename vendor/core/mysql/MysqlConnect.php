<?php

namespace core\mysql;

class MysqlConnect {
	
	/**
	 *
	 * @var type MysqlConnect
	 */
	protected $connect;

	/**
	 * Singleton methods
	 * 
	 * @param type $config $config['db']
	 * @return type MysqlConnect 
	 */
	protected function connect($config)
	{
		if (null === $this->connect)
		{
			$this->connect = new \PDO(
				$config['driver'] . 
				":host=" . $config['host'] . 
				";dbname=" . $config['dbname'] .
				";charset=" . $config['charset'] ,
				$config['user'],
				$config['password'],
				$config['options']
			);
		}
		return $this->connect;
	}
}
