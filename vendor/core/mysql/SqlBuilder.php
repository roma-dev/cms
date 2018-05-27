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
     * Запускает отправку подготовленного запроса в бд и возвращает результат
     *
     * @return mixed
     */
    public function go(){

        $preparePattern = $this->getPreparePattern();
        $prepareParams = $this->getPrepareParams();

        $this->stmt = $this->pdo->prepare($preparePattern);

        foreach ($prepareParams as $key => &$prepareParam) {

            $this->stmt->bindParam($key, $prepareParam);
        }

        $this->stmt->execute();

        $this->clearBuilder(); // очищаем контейнеры строителя

        return $this->stmt->fetchAll();
    }

    /**
     * Очищает переменные строителя sql-запросов
     */
    public function clearBuilder(){

        $this->sqlValues = [];
        $this->prepareParams = [];
        $this->preparePattern = '';
    }

}