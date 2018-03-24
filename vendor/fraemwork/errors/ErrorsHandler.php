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
			'EXCEPTION'			=> 'EXCEPTION',
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
		
		// включаем буферизацию вывода. 
		// Это необходимо, чтоб перехватить вывод фатальной ошибке в браузер
		ob_start();
		
		// устанавливаем общий обработчик ошибок
		set_error_handler([$this, 'errorHandler']);
		
		// устанавливаем обработчик фатальных ошибок
		register_shutdown_function([$this, 'fatalErrorHandler']);
		
		// устанавливаем обработчик исключений
		set_exception_handler([$this, 'exceptionHandler']);
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
	
	/**
	 * Метод отлавливающий фатальные ошибки
	 * 
	 */
	
	public function fatalErrorHandler()
	{
		// получаем последнюю совершенную ошибку
		$lastError = error_get_last();
		
		// если была совершена ошибка и тип этой ошибки совпадает с перечисленными в условии
		if( !empty($lastError) AND $lastError['type'] & ( E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR) )
		{
			// очищаем буфер обмена в котором находится информация о фатальной ошибке
			ob_end_clean();
			
			// рендерим страницу ошибки
			$this->renderError(
					$lastError['type'], 
					$lastError['message'], 
					$lastError['file'], 
					$lastError['line']
				);
			
		}
		else
		{
			// если ошибка не фатальна
			// или если скрипт был завершен без ошибки то выводим содержимое буфера в браузер
			ob_end_flush();
		}
		
	}


	public function exceptionHandler($exception)
	{
		$this->renderError(
				'EXCEPTION', 
				$exception->getMessage(), 
				$exception->getFile(), 
				$exception->getLine(), 
				!$exception->getCode() ?: $exception->getCode()
			);
		
	}

	/**
	 * Метод отображения ошибки
	 * 
	 * @param type $errno Тип ошибки
	 * @param type $errstr Сообщение ошибки
	 * @param type $errfile Файл в котором была совершена ошибка
	 * @param type $errline Строка на которой была совершена ошибка
	 * @param type $code Код ответа сервера
	 */
	
	public function renderError($errno, $errstr, $errfile, $errline, $code = 500)
	{
		// при фатальной ошибке переменная $code содержит булевое значение true
		$code = is_bool($code) ? 500 : $code;
		
		// отправляем заголовок кода ответа сервера
		http_response_code($code);
		
		// записываем сообщение об ошибке в логи
		$this->loggerError($errno, $errstr, $errfile, $errline, $code);
		
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
	
	/**
	 * Метод сохраняющий сообщения об ошибках в логах
	 * 
	 */
	
	public function loggerError($errno, $errstr, $errfile, $errline, $code = 500)
	{
		$userAgent	= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']	: 'UNDERFINED';
		$refferer	= isset($_SERVER['HTTP_REFERER'])	 ? $_SERVER['HTTP_REFERER']		: 'UNDERFINED';
		$requestUri = isset($_SERVER['REQUEST_URI'])	 ? $_SERVER['REQUEST_URI']		: 'UNDERFINED';
		$dateError	= date('Y-m-d H:i:s');
		
		switch ($code)
		{
			case 403;
				$logsFile = APPDIR . '/logs/forbbiden.log';
			break;
			case 404;
				$logsFile = APPDIR . '/logs/notfound.log';
			break;
			default:
				$logsFile = APPDIR . '/logs/errors.log';
		}
		
		// составляем текст лога
		$textError = 
			"[DATE] ............ " . $dateError . "\n" .
			"[USER] ............ " . $userAgent . "\n" .
			"[REFERER] ......... " . $refferer . "\n" .
			"[REQUEST] ......... " . $requestUri . "\n" .
			"[STATUS CODE] ..... " . $code . "\n" .
			"[ERROR TYPE] ...... " . $this->errors[$errno] . "\n" .
			"[ERROR MESSAGE] ... " . $errstr . "\n" .
			"[ERROR FILE] ...... " . $errfile . "\n" .
			"[ERROR LINE] ...... " . $errline . "\n" .
			"______________________________________________________________________\n\n";
		
		// записываем лог в файл
		error_log($textError, 3, $logsFile);
		
	}
	
}
