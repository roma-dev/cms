<?php

/**
 * Класс для автоматической загрузки классов вызываемых в приложении
 *
 * @author xeup
 */
class Autoload {
	
	/**
	 * Инициализирует автозагрузчик
	 */
	
	public static function register(){
		//регистрируем функцию автозагрузки
		spl_autoload_register([__CLASS__, 'loader'], true, true);
	}
	
	
	/**
	 * Автозагрузчик классов
	 * 
	 * @param string $className Имя класса
	 * @return boolean
	 */
	public static function loader($className){
		
		// изменяем неймспейсные слеши на слеши директорий
		$className = str_replace('\\', '/', $className);
		
		// разбиваем строку с классом и неймспейсами в массив
		$classNamespace = explode('/', $className)[0];
		
		if ($classNamespace) {
			
			$config = require APPDIR . '/config/config.php';
			
			// ищем директорию неймспейса в списке директорий
			foreach ($config['autoloadListFolder'] as $nameFolder => $folder) {
				
				if ($nameFolder == $classNamespace) {
				
					// строим полный путь до файла класса
					$classFile = ROOTDIR . $folder . $className . '.php';
					
					// проверяем наличие файла и доступность его к чтению
					if( is_readable($classFile) ){
						require_once $classFile;
						return true;
					}
				}
			}
		}
		// если не получилось подгрузить класс, это приведет выбрасыванию исключения
		return false;
	}
	
	
}
