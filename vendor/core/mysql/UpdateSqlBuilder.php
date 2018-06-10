<?php

namespace core\mysql;


class UpdateSqlBuilder extends SqlBuilder {
	
		/**
	 * Устанавливает статус запроса в UPDATE
	 * 
	 * @return UpdateSqlBuilder
	 */
	public function update(){
		
		$this->sqlValues['action'] = 'update';

        $this->preparePattern .= 'UPDATE';

        return $this;
	}
	
	/**
	 * Устанавливает имя таблица для запроса
	 * 
	 * @param String $tablename
	 * @return UpdateSqlBuilder
	 */
	public function tablename($tablename) {
		
		if (!is_string($tablename)) {
			return false;
		}
		
		$this->sqlValues['tablename'] = $tablename;

        $this->preparePattern .= ' ' . $tablename;

        return $this;
	}
	
	/**
	 * Устанавливает значение колонок для обновления данных
	 * 
	 * @param String $column
	 * @param Mixed $value
	 * @return bool|UpdateSqlBuilder
	 */
	public function set($column, $value) {
		
		if (!is_string($column)) {
			return false;
		}
		
		$column = trim($column);

		$patternName = ':set_' . $column;
		
		$this->prepareParams[$patternName] = $value; // вставляем в массив подготовленных значений
		
		if (isset($this->sqlValues['set'])) { // ставим запятую если это не первый вызыванный set
			$this->preparePattern .= ',';
		} else {
			$this->preparePattern .= ' SET ';
		}
		
		$this->preparePattern .= ' ' . $column . ' = ' . $patternName;
		
		$this->sqlValues['set'][] = [
			'column' => $column,
			'value'	 => $value
		];
		
		return $this;
	}
	
}
