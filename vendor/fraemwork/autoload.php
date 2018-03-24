<?php
/**
 * 
 * @param type string class name
 * @return void
 */

function functionAutoloadClasses($className, $prefix = '.php') {
	// изменяем неймспейсные слеши на слеши директорий
	$className = str_replace('\\', '/', $className) . '.php';
	
	// разбиваем строку с классом и неймспейсами в массив
	$arrayNamespace = explode('/', $className);
	
	// если первый элемент fraemwork, то значит это попытка загрузить класс ядра фраемворка
	if($arrayNamespace[0] == 'fraemwork'){
		
		// строим полный путь до файла класса
		$classFile = ROOTDIR . '/vendor/' . $className;
		
		// проверяем наличие файла и доступность его к чтению
		if( is_readable($classFile) ){
			// если все норм, то пытаемся встроить класс в скрипт
			require_once ROOTDIR . '/vendor/' . $className;
		}
	}
	// если первый элемент app, то значит это попытка загрузить класс приложения
	if($arrayNamespace[0] == 'app'){
		
		// строим полный путь до файла класса
		$classFile = ROOTDIR . '/' . $className;	
		
		// проверяем наличие файла и доступность его к чтению
		if( is_readable($classFile) ){
			// если все норм, то пытаемся встроить класс в скрипт
			require_once ROOTDIR . '/' . $className;
		}
	}
	
	// возвращаем false если класс был вызван в другом неймспейсе
	return false;
}

//регистрируем функцию автозагрузки
spl_autoload_register('functionAutoloadClasses');
