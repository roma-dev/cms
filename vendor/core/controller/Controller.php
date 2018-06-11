<?php

namespace core\controller;


use core\view\View;

class Controller {

	/** @var Array Текущие параметры запроса*/
	public $routers = [];

	/** @var String Текущий вид */
	public $view = '';
	
	/** @var String Текущий шаблон */
	public $layout = '';
	
	/** @var Array Переданные переменные из контроллера */
	public $vars = [];
	
	/**
	 * 
	 * @param String $view
	 * @param String $layout
	 */
	public function __construct($routers, $view, $layout){
		$this->routers	= $routers;
		$this->view		= $view;
		$this->layout	= $layout;
	}

	/**
	 * Инициализирует объект вида
	 * 
	 * @return \core\view\View Объект вида
	 */
	public function initView(){
		return new View(
				$this->routers,
				$this->view,
				$this->layout,
				$this->vars
			);
	}
	
	/**
	 * Проверяет были ли отправлен запрос Ajax'ом
	 * 
	 * @return boolean
	 */
	public function isAjax(){
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		}
	}
}
