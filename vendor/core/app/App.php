<?php

namespace core\app;


use core\mysql\Mysql;
use core\router\Router;

class App{

    /** @var $this */
    public static $app;

    /** @var Mysql */
    public static $db;

    /** @var array контейнер объектов приложения */
    private $container = [];

    /**
     * Создает объект приложения (синглетон)
     *
     * @param array $config
     * @return App|object
     */
    public static function init($config){

        if (null === self::$app) {

            self::$app = new self();
            self::$app->container['config'] = $config;
			self::$app->container['routers'] = Router::parse($config['routes']);
			self::$app->container['defaultLayout'] = $config['defaultLayout'];
            self::$db = new Mysql($config['db']);
        }

        return self::$app;
    }

    /**
     * Сеттер
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value) {

        self::$app->container[$name] = $value;
    }

    /**
     * Геттер
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {

        return self::$app->container[$name];
    }


    /**
     * Запускает экшен из запроса
     *
     * @return void
     */
    public function goAction() {

        if (self::$app->container['routers']) {

			$controllersNamespace = self::$app->container['config']['controllersNamespace'];
		
			$controller = ucfirst(self::$app->container['routers']['controller']) . 'Controller';
			
			$action = 'action' . ucfirst(self::$app->container['routers']['action']);
			
            $controllerClass = $controllersNamespace . $controller;
			
			$currentController = new $controllerClass(
					self::$app->container['routers'], 
					self::$app->container['routers']['action'],
					self::$app->container['defaultLayout']
				);
			
			$currentController->$action();
			
			$view = $currentController->initView();

			$html = $view->render();
			
			echo $html;
        }
    }
}