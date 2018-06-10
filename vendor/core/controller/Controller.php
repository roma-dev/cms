<?php

namespace core\controller;


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
		$this->routers = $routers;
		$this->view = $view;
		$this->layout = $layout;
	}

	
	/**
	 * Рендерит вид и шаблон и выдает контент
	 * 
	 * @param Array Переменные переданные
	 * @return String Отрендеренный контент
	 */
	public function render(){
		
		if ($this->view === false) { // если мы хотим передать данные сразу из экшена
			die();
		}
		
		$layoutPath = APPDIR . '/views/layouts/' . $this->layout . '.php';
		
		if (!is_file($layoutPath)) {
			throw new \Exception('Не найден файл шаблона ' . $layoutPath);
			die();
		}
		
		// включаем буферизацию для шаблона
		ob_start();
		
		try{
			
			$viewPath = APPDIR . '/views/' . $this->routers['controller'] . '/' . $this->view . '.php'; 

			if (!is_file($viewPath)) {
				throw new \Exception('Не найден файл вида ' . $viewPath);
				die();
			}
			
			extract($this->vars);

			try{
				// включаем буферизацию для вида
				ob_start();

				// встраиваем файл вида
				require $viewPath;

			} catch (Exception $e) {
				
				\ob_end_clean();
				throw $e->setMessage('Ошибка при выводе буфере вида ' . $viewPath);
			}

			// выводим содержимое вида
			$content = ob_get_clean();
			
			// выводим файл макета
			require $layoutPath;
			
		} catch (Exception $e) {

			\ob_end_clean();
			throw $e->setMessage('Ошибка при выводе буфере шаблона ' . $layoutPath);
		}
		
		// отдаем содержимое макета
		return ob_get_clean();
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
