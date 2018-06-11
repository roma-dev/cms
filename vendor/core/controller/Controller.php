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
	
	/** @var Array Асс массив css файлов для вставки в шаблон */
	public $cssFiles = [];

	/** @var Array Асс. массив js файлов для вставки в шаблон */
	public $jsFiles = [];

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
				$this->vars,
				$this->cssFiles,
				$this->jsFiles
			);
	}
	
	/**
	 * Добавляет css файл для вставки в шаблон или вид
	 * 
	 * @param String $cssFile
	 * @param String $block В каком блоке будет выводится файл
	 * 
	 * @return void
	 */
	public function addCssFile($cssFile, $block = 'head'){
		
		if (!is_string($cssFile)) {
			throw new \Exception('Файл css задан не строкой');
		}
		
		if (!is_string($block) || $block != 'head' && $block != 'footer') {
			throw new \Exception('Блок вставки css файла задан некорректно.');
		}
		
		$this->cssFiles[$block][] = $cssFile;
	}

	/**
	 * Добавляет js файл для вставки в шаблон или вид
	 * 
	 * @param String $jsFile
	 * @param String $block В каком блоке будет выводится файл
	 * 
	 * @return void
	 */
	public function addJsFile($jsFile, $block = 'head'){
		
		if (!is_string($jsFile)) {
			throw new \Exception('Файл js задан не строкой');
		}
		
		if (!is_string($block) || $block != 'head' && $block != 'footer') {
			throw new \Exception('Блок вставки js файла задан некорректно.');
		}
		
		$this->jsFiles[$block][] = $jsFile;
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
