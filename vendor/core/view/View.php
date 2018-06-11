<?php

namespace core\view;


use core\app\App;

class View {
	
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
	 * Конструктор вида. Получает аргументы из контроллера
	 * 
	 * @param Array $routers
	 * @param String $view
	 * @param String $layout
	 * @param Array $vars
	 * @param Array $cssFiles
	 * @param Array $jsFiles
	 */
	public function __construct($routers, $view, $layout, $vars, $cssFiles, $jsFiles) {
		$this->routers	= $routers;
		$this->view		= $view;
		$this->layout	= $layout;
		$this->vars		= $vars;
		$this->cssFiles = $cssFiles;
		$this->jsFiles  = $jsFiles;
	}

	/**
	 * Выводит блок HEAD перед закрывающимся тегом HEAD
	 * 
	 * @return String
	 */
	public function blockHead(){
		
		$returnString = '';
		
		$returnString .= $this->getJsFiles('head') . PHP_EOL;
		
		$returnString .= $this->getCssFiles('head') . PHP_EOL;
		
		return $returnString;
	}

	/**
	 * Выводит блок перед закрывающимся тегом BODY
	 * 
	 * @return String
	 */
	public function blockFooter(){
		
		$returnString = '';
		
		$returnString .= $this->getJsFiles('footer') . PHP_EOL;
		
		$returnString .= $this->getCssFiles('footer') . PHP_EOL;
		
		return $returnString;
	}
	
	
	private function getCssFiles($block){
		
		if (!is_string($block) || $block != 'head' && $block != 'footer') {
			throw new \Exception('Блок для получения css файлов задан некорректно');
		}
		
		$returnString = '';
		
		$directory = App::$app->config['defaultDirectoryCss'];
				
		if (isset($this->cssFiles[$block])) {
			
			foreach ($this->cssFiles[$block] as $cssFile) {

				$returnString .= PHP_EOL . '<link rel="stylesheet" type="text/css" href="/' . $directory . '/' . $cssFile . '.css">';
			}
		}
		
		return $returnString;
	}

	private function getJsFiles($block){
		
		if (!is_string($block) || $block != 'head' && $block != 'footer') {
			throw new \Exception('Блок для получения js файлов задан некорректно');
		}
		
		$returnString = '';
		
		$directory = App::$app->config['defaultDirectoryJs'];
			
		if (isset($this->jsFiles[$block])) {
			
			foreach ($this->jsFiles[$block] as $jsFile) {

				$returnString .= PHP_EOL . '<script src="/' . $directory . '/' . $jsFile . '.js"></script>';
			}
		}
		
		return $returnString;
	}
	
	/**
	 * Рендерит вид и шаблон и выдает контент
	 * 
	 * @param Array Переменные переданные
	 * @return String Отрендеренный контент
	 */
	public function render(){
		
		if ($this->view === false) { // если мы хотим передать данные сразу из экшена. не хотим рендерить шаблон и вид
			die();
		}
		
		// включаем буферизацию для шаблона
		ob_start();
		
		try{

			try{
				// включаем буферизацию для вида
				ob_start();
				
				$viewPath = APPDIR . '/views/' . $this->routers['controller'] . '/' . $this->view . '.php'; 

				if (!is_file($viewPath)) {
					\ob_end_clean();
					throw new \Exception('Не найден файл вида ' . $viewPath);
				}

				extract($this->vars);
				
				// встраиваем файл вида
				require $viewPath;

			} catch (Exception $e) {
				\ob_end_clean();
				throw $e->setMessage('Ошибка при выводе буфере вида ' . $viewPath);
			}

			// выводим содержимое вида
			$content = ob_get_clean();
			
			if ($this->layout) { // если рендер шаблона не установлен в false. выводим шаблон
				
				$layoutPath = APPDIR . '/views/layouts/' . $this->layout . '.php';
		
				if (!is_file($layoutPath)) {
					\ob_end_clean();
					throw new \Exception('Не найден файл шаблона ' . $layoutPath);
				}
		
				require $layoutPath;
				
			} else { // если рендерится должен только вид
				
				echo $content; 
			}
			
		} catch (Exception $e) {
			\ob_end_clean();
			throw $e->setMessage('Ошибка при выводе буфере шаблона ' . $layoutPath);
		}
		
		// отдаем содержимое макета
		return ob_get_clean();
	}
}
