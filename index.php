<?php

use fraemwork\errors\ErrorsHandler;

// вставляем файл констант
require_once 'constants.php';
// инициализируем автозагрузчик классов
require_once 'autoload.php';

$error = new ErrorsHandler();
echo $test;