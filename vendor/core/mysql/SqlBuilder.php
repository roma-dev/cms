<?php

namespace core\mysql;


/**
 * Класс для построения SQL-запросов
 *
 * Class SqlBuilder
 * @package core\mysql
 */
class SqlBuilder{

    /** @var string Для хранения подготовленного запроса */
    protected $preparePattern = '';

    /** @var array Массив значений для подготовленного запроса */
    protected $prepareParams = [];

    /** @var array Массив введенных в SQL значений */
    protected $sqlValues = [];

	/** @var Integer Количество строк затронутых последним запросом к бд */
	protected $rowCount = 0;
	
	
	/**
     * Выводит массив для подготовленного запроса
     *
     * @return array
     */
    public function getPrepareParams() {
        return $this->prepareParams;
    }

    /**
     * Выводит шаблон для подготовленного запроса
     *
     * @return string
     */
    public function getPreparePattern(){
        return $this->preparePattern;
    }
	
	/**
     * Выводит значения sql-запроса
     * @return array
     */
    public function getSqlValues(){
        return $this->sqlValues;
    }

	/**
     * Выводит количество строк затронутых sql-запросом
     * @return array
     */
    public function getRowCount(){
        return $this->rowCount;
    }
	
    /**
     * Запускает отправку подготовленного запроса в бд и возвращает результат
     *
     * @return mixed
     */
    public function go(){

		if ($this->sqlValues['action'] == 'insert') { // если статус запроса вставка INSERT
			
			$preparePattern = $this->getPreparePattern();
			
			$preparePattern .= ' (' . $this->insertColumnsPattern . ') VALUES (' . $this->insertValuesPattern . ')';
			
		} else {
			
			$preparePattern = $this->getPreparePattern();
		}

		$prepareParams = $this->getPrepareParams();
		
		$this->stmt = $this->pdo->prepare($preparePattern);

        foreach ($prepareParams as $key => &$prepareParam) { // добавляем значения в подготовленный запрос

            $this->stmt->bindParam($key, $prepareParam);
        }


		$result = $this->stmt->execute(); // если запрос не вставка то выведется количество затронутых строк

		if ($this->sqlValues['action'] == 'select') { // если статус запроса выборка
		
			$result = $this->stmt->fetchAll();
		}
		
		$this->rowCount = $this->stmt->rowCount(); // количество строк
		
		$this->clearBuilder(); // очищаем контейнеры строителя
		
		return $result;
    }

    /**
     * Очищает переменные строителя sql-запросов
     */
    public function clearBuilder(){

        $this->sqlValues = [];
        $this->prepareParams = [];
        $this->preparePattern = '';
		$this->insertColumnsPattern = '';
		$this->insertValuesPattern = '';
    }

}