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
			self::$app->routers = Router::parse($config['routes']);
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

            $controller = self::$app->container['config']['controllersNamespace'] . self::$app->container['routers']['controller'];
            $action = self::$app->container['routers']['action'];

            // если по какой-то причине не произойдет вызов экшена то отдаем код сервера "Внутренняя ошибка сервера"
            if (call_user_func_array([new $controller(),  $action], []) === false) {

                throw new \Exception('Неправильно задан контроллер или экшен');
            }

        }
    }

    private function __construct(){}
    private function __clone(){}

}