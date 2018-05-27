<?php

use core\errors\ErrorsHandler;
use core\app\App;

// устанавливаем московское время
ini_set('date.timezone', 'Europe/Moscow');

// определяем режим работы проекта
defined('DEBUG') or define('DEBUG', false);
// определяем корневую директорию проекта
defined('ROOTDIR') or define('ROOTDIR', dirname(__DIR__));
// директория приложения
defined('APPDIR') or define('APPDIR', ROOTDIR . '/app');

// файл конфигурации
$config = require APPDIR . '/config/config.php';

// инициализируем автозагрузчик классов
require_once ROOTDIR . '/vendor/core/autoload/Autoload.php';

// регистрируем автозагрузчик
Autoload::register();

// регистируем обработчик ошибок
ErrorsHandler::register($config['errorHandler']);

App::init($config);

App::$app->routers = core\router\Router::parse($config['routes']);

App::$app->goAction();
