<?php

namespace core\mysql;

class InsertIntoSqlBuilder extends SqlBuilder{
	
	/** @var String Для хранения паттерна колонок */
	protected $insertColumnsPattern = '';
	
	/** @var String Для хранения паттерна значений */
	protected $insertValuesPattern = '';

	/**
	 * Выдает паттерн колонок вставки
	 * 
	 * @return String 
	 */
	public function getInsertColumnsPattern(){
		return $this->insertColumnsPattern;
	}
	
	/**
	 * Выдает паттер значений вставки
	 * 
	 * @return String
	 */
	public function getInsertValuesPattern(){
		return $this->insertValuesPattern;
	}

	/**
	 * Выдает полный паттерн подготовленного запроса для запроса вставки
	 * 
	 * @return String
	 */
	public function getAllInsertPattern(){
		return $this->preparePattern .= ' (' . $this->insertColumnsPattern . ') VALUES (' . $this->insertValuesPattern . ')'; 
	}
	
	/**
	 * Устанавливает статус запроса в INSERT
	 * 
	 * @return InsertIntoSqlBuilder
	 */
	public function insert(){
		
		$this->sqlValues['action'] = 'insert';

        $this->preparePattern .= 'INSERT';

        return $this;
	}
	
	/**
	 * Устанавливает имя таблицы для вставки
	 * 
	 * @param String $tablename
	 * @return bool|InsertIntoSqlBuilder
	 */
	public function into($tablename){
		
		if (!is_string($tablename)) {
			return false;
		}
		
		$this->sqlValues['into'] = trim($tablename);
		
		$this->preparePattern .= ' INTO ' . trim($tablename);
        
        return $this;
	}
	
	/**
	 * Устанавливает колонку и значение для вставки
	 * 
	 * @param String $column
	 * @param Mixed $value
	 * 
	 * @return bool|InsertIntoSqlBuilder
	 */
	public function columnValue($column, $value){
		
		if (!is_string($column)) {
			return false;
		}
		
		$column = trim($column);
		
		$this->sqlValues['columns'][] = [
			'column' => $column,
			'value'	 => $value
		];
		
		if ($this->insertColumnsPattern) {
			$this->insertColumnsPattern .= ', ';  
		}
		
		$this->insertColumnsPattern .= $column;
		
		$this->prepareParams[':columnValue_' . $column] = $value;
		
		if ($this->insertValuesPattern) {
			$this->insertValuesPattern .= ', '; 
		}
		
		$this->insertValuesPattern .= ':columnValue_' . $column;
		
		return $this;
	}
}
