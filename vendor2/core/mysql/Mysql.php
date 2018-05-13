<?php

namespace core\mysql;

class Mysql extends MysqlConnect{
	
	public function __construct($config) 
	{
		$this->connect($config);
	}
	
}
