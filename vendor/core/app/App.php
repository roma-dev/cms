<?php
/**
 * Created by PhpStorm.
 * User: xeup
 * Date: 21.05.18
 * Time: 21:15
 */

namespace core\app;

class App{

    /** @var object $this */
    public static $object;

    /** @var array контейнер объектов приложения */
    private $container = [];

    /**
     * Создает объект приложения (синглетон)
     *
     * @param array $config
     * @return App|object
     */
    public static function init($config){

        if (null === self::$object) {

            self::$object = new self();
            self::$object->container['config'] = $config;
        }

        return self::$object;
    }

    /**
     * Сеттер
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value) {

        self::$object->container[$name] = $value;
    }

    /**
     * Геттер
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {

        return self::$object->container[$name];
    }


    /**
     * Запускает экшен из запроса
     *
     * @return void
     */
    public function goAction() {

        if (self::$object->container['routers']) {

            $controller = self::$object->container['config']['controllersNamespace'] . self::$object->container['routers']['controller'];
            $action = self::$object->container['routers']['action'];

            // если по какой-то причине не произойдет вызов экшена то отдаем код сервера "Внутренняя ошибка сервера"
            if (call_user_func_array([new $controller(),  $action], []) === false) {

                throw new Exception('Неправильно задан контроллер или экшен');
            }

        }
    }

    private function __construct(){}
    private function __clone(){}

}