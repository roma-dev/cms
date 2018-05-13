<?php
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
require_once ROOTDIR . '/vendor2/core/autoload/Autoload.php';

// регистрируем автозагрузчик
Autoload::register();

// регистируем обработчик ошибок
core\errors\ErrorsHandler::register($config['errorHandler']);