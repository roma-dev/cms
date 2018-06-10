<?php

namespace core\mysql;


class DeleteSqlBuilder extends SqlBuilder {
	
	/**
	 * Устанавливает статус запроса в DELETE
	 * 
	 * @return DeleteSqlBuilder
	 */
	
	public function delete(){
		
		$this->sqlValues['action'] = 'delete';

        $this->preparePattern .= 'DELETE';

        return $this;
	}
	
}
