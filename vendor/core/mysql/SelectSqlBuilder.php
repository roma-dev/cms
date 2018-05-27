<?php

namespace core\mysql;


class SelectSqlBuilder extends SqlBuilder {

    /**
     * Устанавливает статус запроса в SELECT
     *
     * @return $this
     */
    public function select(){

        $this->sqlValues['action'] = 'select';

        $this->preparePattern .= 'SELECT';

        return $this;
    }

    /**
     * Добавляет конструкцию DISTINCT к конструкции SELECT
     *
     * @return $this
     */
    public function distinct(){

        if ($this->sqlValues['action'] == 'select') {

            $this->preparePattern .= ' DISTINCT';
        }

        return $this;
    }

    /**
     * Устанавливает с какими колонками будет работать запрос
     *
     * @param array|string $columnNames
     * @return bool|$this
     */
    public function columns($columnNames){

        if (is_string($columnNames)) {
            $columnNames = explode(',', $columnNames);
        }

        if (is_array($columnNames)) {

            $returnString = '';

            foreach ($columnNames as $index => $columnName) {

                $returnString .= trim($columnName);

                if (isset($columnNames[$index + 1])) { // если это не последний элемент массива ставим запятую
                    $returnString .= ', ';
                }
            }

            $this->preparePattern .= ' ' . trim($returnString);

            return $this;
        }

        return false;
    }

    /**
     * Указывает из каких таблиц будет произведена выборка
     *
     * @param array|string $tableNames
     * @return bool|$this
     */
    public function from($tableNames){

        if (is_string($tableNames)) {

            $tableNames = explode(',', $tableNames);
        }

        if (is_array($tableNames)) {

            $returnString = '';

            foreach ($tableNames as $index => $tableName) {

                $returnString .= trim($tableName);

                if (isset($tableNames[$index + 1])) { // если это не последний элемент массива ставим запятую
                    $returnString .= ', ';
                }
            }

            $this->preparePattern .= ' FROM ' . trim($returnString);

            return $this;
        }

        return false;
    }

    /**
     * Устанавливает имя таблицы для джоин-запроса
     *
     * @param string $tablename
     * @return bool|$this
     */
    public function join($tablename){

        if (!is_string($tablename)) {
            return false;
        }

        $this->preparePattern .= ' INNER JOIN ' . $tablename;

        return $this;
    }

    /**
     * Добавляет условие для связывания двух таблиц в джоин-запросах
     *
     * @param string $condition1
     * @param string $condition2
     * @return bool|$this
     */
    public function on($condition1, $condition2){

        if (!is_string($condition1) || !is_string($condition2)) {
            return false;
        }

        $this->preparePattern .= ' ON ' . $condition1 . ' = ' . $condition2;

        return $this;
    }

    /**
     * Отрабатывает внутри публичных методов where
     *
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $not
     * @param string $where
     * @return bool|string
     */
    private function whereBuilder($column, $values, $condition = '=', $not = '', $where = 'where'){

        if (!is_string($column) || !is_string($condition) || !is_string($not)) {
            return false;
        }

        $condition = strtolower($condition); // преобразуем в нижний регистр
        $not = strtolower($not);
        $where = strtolower($where);

        if ($where == 'where') { // выбираем тип where
            $this->preparePattern .= ' WHERE';
        } elseif ($where == 'and') {
            $this->preparePattern .= ' AND';
        } elseif ($where == 'or') {
            $this->preparePattern .= ' OR';
        } else {
            return false;
        }

        if ($not === 'not') { // если условие имеет отрицающий контекст
            $this->preparePattern .= ' NOT';
        }

        if (isset($this->sqlValues['where'])) { // получаем индекс для создания уникального имени в паттерне
            $index = count($this->sqlValues['where']);
        } else {
            $index = 0;
        }

        switch ($condition) {

            case 'like':

                $this->whereLike($column, $values, $index, $not);

                return $this;

            case 'between':

                if (!is_array($values) || is_array($values) && count($values) != 2){
                    return false;
                }

                $this->whereBetween($column, $values, $index, $not);

                return $this;

            case 'in':

                if (!is_array($values)) {
                    return false;
                }

                $this->whereIn($column, $values, $index, $not);

                return $this;

            case 'is':

                if (strtolower($values) != 'null') { // это выражение работает только с null
                    return false;
                }

                $this->preparePattern .= ' ' . $column . ' IS NULL';

                return $this;

            default: // если мы тут значит сработал обычное сравнивание = != <> < > <= >= <=> и тд.

                if ($condition == '=' || $condition == '!=' || $condition == '<>' || $condition == '<'
                    || $condition == '>' || $condition == '<=' || $condition == '>=' || $condition == '<=>') {

                    $this->whereDefault($column, $values, $condition, $index, $not);

                    return $this;
                }

                return false;
        }
    }

    /**
     * @param string $column
     * @param mixed $values
     * @param string $index
     * @param string $not
     * @return void
     */
    private function whereLike($column, $values, $index, $not){

        $this->sqlValues['where'][$index] = [
            'column'    => $column,
            'values'    => $values,
            'condition' => 'like',
            'not'       => $not,
        ];

        $patternName = ':where_like_' . $column . '_' . $index;

        $this->prepareParams[$patternName] = $values;

        $this->preparePattern .= ' ' . $column . ' LIKE ' . $patternName;
    }

    /**
     * @param string $column
     * @param mixed $values
     * @param string $index
     * @param string $not
     * @return void
     */
    private function whereBetween($column, $values, $index, $not){

        $this->sqlValues['where'][$index] = [
            'column'    => $column,
            'values'    => $values,
            'condition' => 'between',
            'not'       => $not,
        ];

        $patternName1 = ':where_between_' . $column . '_' . $index . '_1';
        $patternName2 = ':where_between_' . $column . '_' . $index . '_2';

        $this->prepareParams[$patternName1] = $values[0];
        $this->prepareParams[$patternName2] = $values[1];

        $this->preparePattern .= ' ' . $column . ' BETWEEN ' . $patternName1 . ' AND ' . $patternName2;
    }

    /**
     * @param string $column
     * @param mixed $values
     * @param string $index
     * @param string $not
     * @return void
     */
    private function whereIn($column, $values, $index, $not){

        $this->sqlValues['where'][$index] = [
            'column'    => $column,
            'values'    => $values,
            'condition' => 'in',
            'not'       => $not,
        ];

        $this->preparePattern .= ' ' . $column . ' IN (';

        foreach ($values as $i => $value) {

            $patternName = ':where_in_' . $column . '_' . $index . '_' . $i;

            $this->prepareParams[$patternName] = $values[$i];

            $this->preparePattern .= $patternName;

            if (isset($values[$i + 1])) { // если это не последний элемент массива ставим запятую
                $this->preparePattern .= ', ';
            }
        }

        $this->preparePattern .= ')';
    }

    /**
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $index
     * @param string $not
     * @return void
     */
    private function whereDefault($column, $values, $condition, $index, $not){

        $this->sqlValues['where'][$index] = [
            'column'    => $column,
            'values'    => $values,
            'condition' => $condition,
            'not'       => $not,
        ];

        $patternName = ':where_' . $column . '_' . $index;

        $this->prepareParams[$patternName] = $values;

        $this->preparePattern .= ' ' . $column . ' ' . $condition . ' ' . $patternName;
    }

    /**
     * Устанавливает значения для конструкции where
     *
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $not
     * @return bool|$this
     */
    public function where($column, $values, $condition = '=', $not = ''){

        return $this->whereBuilder($column, $values, $condition, $not);
    }

    /**
     * Устанавливает значения для конструкции where and
     *
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $not
     * @return bool|$this
     */
    public function andWhere($column, $values, $condition = '=', $not = ''){

        return $this->whereBuilder($column, $values, $condition, $not, 'and');
    }

    /**
     * Устанавливает значения для конструкции where or
     *
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $not
     * @return bool|$this
     */
    public function orWhere($column, $values, $condition = '=', $not = ''){

        return $this->whereBuilder($column, $values, $condition, $not, 'or');
    }

    /**
     * Устанавливает значения для конструкции GROUP BY
     *
     * @param string Строка колонок через запятую
     * @return false|$this
     */
    public function groupBy($columnNames){

        if (is_string($columnNames)) {
            $columnNames = explode(',', $columnNames);
        }

        if (is_array($columnNames)) {

            $returnString = ' GROUP BY ';

            foreach ($columnNames as $index => $columnName) {

                $returnString .= trim($columnName);

                if (isset($columnNames[$index + 1])) { // если это не последний элемент массива ставим запятую
                    $returnString .= ', ';
                }
            }

            $this->preparePattern .= ' ' . trim($returnString);

            return $this;
        }

        return false;
    }

    /**
     * Строитель конструкции HAVING
     *
     * @param string $aggregateFunction
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $not
     * @param string $having
     * @return bool|SqlBuilder
     */
    public function havingBuilder($aggregateFunction, $column, $values, $condition = '=', $not = '', $having = 'having'){

        if (!is_string($aggregateFunction) || !is_string($column) || !is_string($condition) || !is_string($not)) {
            return false;
        }

        $condition = strtolower($condition); // преобразуем в нижний регистр
        $not = strtolower($not);
        $where = strtolower($having);
        $aggregateFunction = strtoupper($aggregateFunction); // преобразуем в верхний регистр

        if ($where == 'having') { // выбираем тип where
            $this->preparePattern .= ' HAVING ';
        } elseif ($where == 'and') {
            $this->preparePattern .= ' AND';
        } elseif ($where == 'or') {
            $this->preparePattern .= ' OR';
        } else {
            return false;
        }

        if ($not === 'not') { // если условие имеет отрицающий контекст
            $this->preparePattern .= ' NOT';
        }

        if (isset($this->sqlValues['having'])) { // получаем индекс для создания уникального имени в паттерне
            $index = count($this->sqlValues['having']);
        } else {
            $index = 0;
        }

        switch ($condition) {

            case 'like':

                $this->havingLike($aggregateFunction, $column, $values, $index, $not);

                return $this;

            case 'between':

                if (!is_array($values) || is_array($values) && count($values) != 2){
                    return false;
                }

                $this->havingBetween($aggregateFunction, $column, $values, $index, $not);

                return $this;

            case 'in':

                if (!is_array($values)) {
                    return false;
                }

                $this->havingIn($aggregateFunction, $column, $values, $index, $not);

                return $this;

            case 'is':

                if (strtolower($values) != 'null') { // это выражение работает только с null
                    return false;
                }

                $this->preparePattern .= $aggregateFunction . '(' . $column . ') IS NULL';

                return $this;

            default: // если мы тут значит сработал обычное сравнивание = != <> < > <= >= <=> и тд.

                if ($condition == '=' || $condition == '!=' || $condition == '<>' || $condition == '<'
                    || $condition == '>' || $condition == '<=' || $condition == '>=' || $condition == '<=>') {

                    $this->havingDefault($aggregateFunction, $column, $values, $condition, $index, $not);

                    return $this;
                }

                return false;
        }
    }

    /**
     * Устанавливает значение для конструкции HAVING
     *
     * @param string $aggregateFunction
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $not
     * @return bool|SqlBuilder
     */
    public function having($aggregateFunction, $column, $values, $condition = '=', $not = ''){

        return $this->havingBuilder($aggregateFunction, $column, $values, $condition, $not, 'having');
    }

    /**
     * Устанавливает значение для конструкции HAVING AND
     *
     * @param string $aggregateFunction
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $not
     * @return bool|SqlBuilder
     */
    public function andHaving($aggregateFunction, $column, $values, $condition = '=', $not = ''){

        return $this->havingBuilder($aggregateFunction, $column, $values, $condition, $not, 'and');
    }

    /**
     * Устанавливает значение для конструкции HAVING OR
     *
     * @param string $aggregateFunction
     * @param string $column
     * @param mixed $values
     * @param string $condition
     * @param string $not
     * @return bool|SqlBuilder
     */
    public function orHaving($aggregateFunction, $column, $values, $condition = '=', $not = ''){

        return $this->havingBuilder($aggregateFunction, $column, $values, $condition, $not, 'or');
    }

    /**
     * @param string $aggregateFunction
     * @param string $column
     * @param string $values
     * @param int $index
     * @param string $not
     */
    public function havingLike($aggregateFunction, $column, $values, $index, $not){

        $this->sqlValues['having'][$index] = [
            'aggregateFunction' => $aggregateFunction,
            'column'            => $column,
            'values'            => $values,
            'condition'         => 'like',
            'not'               => $not,
        ];

        $patternName = ':having_like_' . $column . '_' . $index;

        $this->prepareParams[$patternName] = $values;

        $this->preparePattern .= $aggregateFunction . '(' . $column . ') LIKE ' . $patternName;
    }

    /**
     * @param string $aggregateFunction
     * @param string $column
     * @param array $values
     * @param int $index
     * @param string $not
     */
    public function havingBetween($aggregateFunction, $column, $values, $index, $not){

        $this->sqlValues['having'][$index] = [
            'aggregateFunction' => $aggregateFunction,
            'column'            => $column,
            'values'            => $values,
            'condition'         => 'between',
            'not'               => $not,
        ];

        $patternName1 = ':having_between_' . $column . '_' . $index . '_1';
        $patternName2 = ':having_between_' . $column . '_' . $index . '_2';

        $this->prepareParams[$patternName1] = $values[0];
        $this->prepareParams[$patternName2] = $values[1];

        $this->preparePattern .= $aggregateFunction . '(' . $column . ') BETWEEN ' . $patternName1 . ' AND ' . $patternName2;
    }

    /**
     * @param string $aggregateFunction
     * @param string $column
     * @param array $values
     * @param int $index
     * @param string $not
     */
    public function havingIn($aggregateFunction, $column, $values, $index, $not){

        $this->sqlValues['having'][$index] = [
            'aggregateFunction' => $aggregateFunction,
            'column'            => $column,
            'values'            => $values,
            'condition'         => 'in',
            'not'               => $not,
        ];

        $this->preparePattern .= $aggregateFunction . '(' . $column . ') IN (';

        foreach ($values as $i => $value) {

            $patternName = ':having_in_' . $column . '_' . $index . '_' . $i;

            $this->prepareParams[$patternName] = $values[$i];

            $this->preparePattern .= $patternName;

            if (isset($values[$i + 1])) { // если это не последний элемент массива ставим запятую
                $this->preparePattern .= ', ';
            }
        }

        $this->preparePattern .= ')';
    }

    /**
     * @param string $aggregateFunction
     * @param string $column
     * @param string $values
     * @param string $condition
     * @param int $index
     * @param string $not
     */
    public function havingDefault($aggregateFunction, $column, $values, $condition, $index, $not){

        $this->sqlValues['having'][$index] = [
            'aggregateFunction' => $aggregateFunction,
            'column'            => $column,
            'values'            => $values,
            'condition'         => $condition,
            'not'               => $not,
        ];

        $patternName = ':having_' . $column . '_' . $index;

        $this->prepareParams[$patternName] = $values;

        $this->preparePattern .= $aggregateFunction . '(' . $column . ') ' . $condition . ' ' . $patternName;
    }

    /**
     * Устанавливает значение для конструкции ORDER BY
     *
     * @param string $columnNames
     * @param string $orderType
     * @return bool|$this
     */
    public function orderBy($columnNames, $orderType = 'ASC'){

        if (is_string($orderType)) {
            $orderType = strtoupper($orderType);
        }

        if (!in_array($orderType, ['ASC', 'DESC'])) {
            return false;
        }

        if (is_string($columnNames)) {
            $columnNames = explode(',', $columnNames);
        }

        if (is_array($columnNames)) {

            if (!isset($this->sqlValues['order'])) {
                $this->preparePattern .= ' ORDER BY ';
            } else {
                $this->preparePattern .= ', ';
            }

            $this->sqlValues['order'][] = [
                'orderType' => $orderType,
                'columnNames' => $columnNames
            ];

            foreach ($columnNames as $index => $columnName) {

                $columnName = trim($columnName);

                $this->preparePattern .= $columnName . ' ' . $orderType;

                if (isset($columnNames[$index + 1])) { // если это не последний элемент массива ставим запятую
                    $this->preparePattern .= ', ';
                }
            }

            return $this;
        }

        return false;
    }

    /**
     * Устанавливет значения для конструкции LIMIT
     *
     * @param int $param1
     * @param int $param2
     * @return bool|$this
     */
    public function limit($param1, $param2 = 0){

        $param1 = (int) $param1;
        $param2 = (int) $param2;

        if ($param1 <= 0 || !is_int($param1) || !is_int($param2) ) {
            return false;
        }

        $this->sqlValues['limit'] = [
            'param1' => $param1,
            'param2' => $param2
        ];

        $this->preparePattern .= ' LIMIT ' . $param1;

        if (!$param2 <= 0) {
            $this->preparePattern .= ', ' . $param2;
        }

        return $this;
    }
}