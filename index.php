<?php

// вставляем файл констант
require_once 'constants.php';

// инициализируем автозагрузчик классов
require_once 'autoload.php';

$error = new fraemwork\errors\ErrorsHandler();

//вставляем файл настроек приложения
require_once APPDIR . '/config/settings.php';