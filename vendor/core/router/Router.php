<?php

namespace core\router;

/**
 * Класс для разбора адреса
 *
 * @author xeup
 */
class Router {
	
	/**
	 * Парсит запрос и выдает текущий контроллер и экшен
	 * 
	 * @param array $config Массив шаблонов роутинга
	 * @return bool|array Распарсенный запрос: контроллер, экшен, алиас, гет параметры
	 */
	public static function parse($routes){
		
		$alias = self::getAlias();
		
		$returnArray = [];
		foreach ($routes as $route => $routeParams) {
			
			if ($route == $alias) {
				
				$returnArray['controller']  = ucfirst($routeParams['controller']) . 'Controller';
				$returnArray['action']		= 'action' . ucfirst($routeParams['action']);
				$returnArray['alias']		= $alias;
				$returnArray['params']		= self::getRequestParams();
				
				return $returnArray;
			}
		}
		
		return false;	
	}
	
	/**
	 * Парсит запрос и возвращает алиас
	 * 
	 * @return string Алиас запроса (обрезает слеши с двух сторон)
	 */
	private static function getAlias(){
		$requestArray = explode('?', $_SERVER['REQUEST_URI']);
		return $requestArray[0];
	}
	
	/**
	 * Парсит запрос и выдает гет параметры
	 * 
	 * @return array Массив гет параметров запроса
	 */
	private static function getRequestParams(){
		
		$queryParamsString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	
		$returnArray = [];
		
		if ($queryParamsString) {

			$queryParamsArray = explode('&', $queryParamsString);

			foreach ($queryParamsArray as $queryParams) {

				$paramsArray = explode('=', $queryParams);

				if (is_array($paramsArray) && count($paramsArray) == 2) {

					$returnArray[$paramsArray[0]] = $paramsArray[1];
				}
			}

			return $returnArray;
		}
		
		return $returnArray;
	}
	
}
