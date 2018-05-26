<?php

namespace core\mysql;


/**
 * Класс для запросов к бд
 *
 * Class Mysql
 * @package core\mysql
 */
class Mysql extends MysqlConnect{
	
	public function __construct($config){
	    // создаем соединение с бд
		$this->connect($config);
	}

}
