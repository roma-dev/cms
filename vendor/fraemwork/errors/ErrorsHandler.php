<?php

namespace fraemwork\errors;

/**
 * Данный класс реализовывает перехват исключений и ошибок
 * 
 */

class ErrorsHandler 
{
	/**
	 * 
	 * @var type array erros type
	 */
	private $errors = 
		[
			E_ERROR				=> 'E_ERROR',
			E_WARNING			=> 'E_WARNING',
			E_PARSE				=> 'E_PARSE',
			E_NOTICE			=> 'E_NOTICE',
			E_CORE_ERROR		=> 'E_CORE_ERROR',
			E_CORE_WARNING		=> 'E_CORE_WARNING',
			E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
			E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
			E_USER_ERROR		=> 'E_USER_ERROR',
			E_USER_WARNING		=> 'E_USER_WARNING',
			E_USER_NOTICE		=> 'E_USER_NOTICE',
			E_STRICT			=> 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED		=> 'E_DEPRECATED',
			E_USER_DEPRECATED	=> 'E_USER_DEPRECATED',
		];
	
	
	public function __construct() 
	{
		if(DEBUG)
		{
			error_reporting(-1);
			
		}else
		{
			error_reporting(0);
		}
		
		// устанавливаем общий обработчик ошибок
		set_error_handler([$this, 'errorHandler']);
	}
	
	
	/**
	 * Функция для отлова всех ошибок
	 * 
	 * @param type $errno
	 * @param type $errstr
	 * @param type $errfile
	 * @param type $errline
	 * @return boolean true
	 */
	
	public function errorHandler($errno, $errstr, $errfile, $errline)
	{
		// выводим вид ошибки в браузер
		$this->renderError($errno, $errstr, $errfile, $errline);
		
		
		// если данная фукнция вернет false, 
		// то управление передасться дальше и случится вывод ошибки в браузер
		// поэтому всегда нужно выдавать true
		return true;
	}
	
	
	public function renderError($errno, $errstr, $errfile, $errline, $code = 500)
	{
		// отправляем заголовок кода ответа сервера
		http_response_code($code);
		
		if(DEBUG)
		{
			// вид для разработчика - для отладки в режиме разработки
			require 'views/error.php';
		}
		else
		{
			// виды для показа пользователю
			switch ($code)
			{
					case 403: // Доступ запрещен
						require APPDIR . '/views/errors/e403.php';
					break;
				
					case 404: // Страницы не существует
						require APPDIR . '/views/errors/e404.php';
					break;
				
					case 503; // Сайт недоступен
						require APPDIR . '/views/errors/e503.php';
					break;
				
					default: // Внутренняя ошибка сервера
						require APPDIR . '/views/errors/e500.php';
			}
			
		}
		
	}
	
}
