<?php

use fraemwork\errors\ErrorsHandler;

// устанавливаем московское время
ini_set('date.timezone', 'Europe/Moscow');

// определяем режим работы проекта
defined('DEBUG') or define('DEBUG', true);
// определяем корневую директорию проекта
defined('ROOTDIR') or define('ROOTDIR', dirname(__DIR__));
// директория приложения
defined('APPDIR') or define('APPDIR', ROOTDIR . '/app');

// файл конфигурации
$config = require_once APPDIR . '/config/config.php';

// инициализируем автозагрузчик классов
require_once ROOTDIR . '/vendor/fraemwork/autoload.php';

// запускаем обработчик ошибок
ErrorsHandler::handler($config['errorHandler']);