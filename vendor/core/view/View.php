<?php

namespace core\view;


class View {
	
	/** @var Array Текущие параметры запроса*/
	public $routers = [];

	/** @var String Текущий вид */
	public $view = '';
	
	/** @var String Текущий шаблон */
	public $layout = '';
	
	/** @var Array Переданные переменные из контроллера */
	public $vars = [];
	
	
	public function __construct($routers, $view, $layout, $vars) {
		$this->routers	= $routers;
		$this->view		= $view;
		$this->layout	= $layout;
		$this->vars		= $vars;
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
