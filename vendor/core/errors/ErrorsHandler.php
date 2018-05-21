<?php

namespace core\errors;

/**
 * Данный класс реализовывает перехват исключений и ошибок
 * 
 */

class ErrorsHandler 
{
	/** @var type ErrorsHandler */
	private static $handler;
	
	/** @var type boolean */
	private $logs;

	/** @var type array */
	private $logFiles;

	/** @var type array */
	private $errorsView;

	/** @var type array erros type */
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
	
	/**
	 * Метод singleton
	 * 
	 * @param type $config array settings
	 * @return type ErrorsHandler
	 */
	
	static function register($config){
		
		if (null === self::$handler) {
			self::$handler				= new self();
			self::$handler->logs		= $config['logs'];
			self::$handler->logFiles	= $config['logFiles'];
			self::$handler->errorsView	= $config['errorsView'];
			
			if (DEBUG) {
				error_reporting(-1);
				ini_set('display_errors', 'On');

			} else {
				error_reporting(0);
				ini_set('display_errors', 'Off');
			}

			// включаем буферизацию вывода. 
			// Это необходимо, чтоб перехватить вывод фатальной ошибке в браузер
			ob_start();

			// устанавливаем общий обработчик ошибок
			set_error_handler([self::$handler, 'errorHandler']);

			// устанавливаем обработчик фатальных ошибок
			register_shutdown_function([self::$handler, 'fatalErrorHandler']);

			// устанавливаем обработчик исключений
			set_exception_handler([self::$handler, 'exceptionHandler']);
	
		}
		return self::$handler;
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
	
	public function errorHandler($errno, $errstr, $errfile, $errline){
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
	
	public function fatalErrorHandler() {
		// получаем последнюю совершенную ошибку
		$lastError = error_get_last();
		
		// если была совершена ошибка и тип этой ошибки совпадает с перечисленными в условии
		if ( !empty($lastError) AND $lastError['type'] & ( E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR) ) {
			// очищаем буфер обмена в котором находится информация о фатальной ошибке
			ob_end_clean();
			
			// рендерим страницу ошибки
			$this->renderError(
					$lastError['type'], 
					$lastError['message'], 
					$lastError['file'], 
					$lastError['line']
				);
			
		} else {
			// если ошибка не фатальна
			// или если скрипт был завершен без ошибки то выводим содержимое буфера в браузер
			ob_end_flush();
		}
		
	}

	/**
	 * Обработчик исключений
	 * 
	 * @param type $exception
	 */
	public function exceptionHandler($exception){
		
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
	
	public function renderError($errno, $errstr, $errfile, $errline, $code = 500){
		// при фатальной ошибке переменная $code содержит булевое значение true
		// а при исключении PPOException содержит строковое значение
		// поэтому нам нужно привести значение к числовому типу
		$code = (int) !is_numeric($code) ? 500 : $code;
		
		// отправляем заголовок кода ответа сервера
		http_response_code($code);
		
		// если логирование включено
		if ($this->logs) {
			// записываем сообщение об ошибке в логи
			$this->loggerError($errno, $errstr, $errfile, $errline, $code);	
		}
		
		if (DEBUG) {
			// вид для разработчика - для отладки в режиме разработки
			require 'views/error.php';
		} else {
			// виды для показа пользователю
			switch ($code) {
					case 403: // Доступ запрещен
						require APPDIR . $this->errorsView['403'];
					break;
				
					case 404: // Страницы не существует
						require APPDIR . $this->errorsView['404'];
					break;
				
					case 503; // Сайт недоступен
						$errstr = 'Сайт временно недоступен';
						require APPDIR . $this->errorsView['503'];
					break;
				
					default: // Внутренняя ошибка сервера
						$errstr = 'Внутренняя ошибка сервера';
						require APPDIR . $this->errorsView['500'];
			}
			
		}
		
	}
	
	/**
	 * Метод сохраняющий сообщения об ошибках в логах
	 * 
	 */
	
	public function loggerError($errno, $errstr, $errfile, $errline, $code = 500){
		$userAgent	= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']	: 'UNDERFINED';
		$refferer	= isset($_SERVER['HTTP_REFERER'])	 ? $_SERVER['HTTP_REFERER']		: 'UNDERFINED';
		$requestUri = isset($_SERVER['REQUEST_URI'])	 ? $_SERVER['REQUEST_URI']		: 'UNDERFINED';
		$dateError	= date('Y-m-d H:i:s');
		// определяем ip с которого был совершен запрос
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = 'UNDERFINED';
		}
		
		switch ($code) {
			case 403;
				$logsFile = APPDIR . $this->logFiles['403'];
			break;
			case 404;
				$logsFile = APPDIR . $this->logFiles['404'];
			break;
			default:
				$logsFile = APPDIR . $this->logFiles['500'];
		}
		
		
		
		
		// составляем текст лога
		$textError = 
			"[DATE] ............ " . $dateError . "\n" .
			"[USER] ............ " . $userAgent . "\n" .
			"[IP] .............. " . $ip . "\n" .
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
