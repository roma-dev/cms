<?php

// определяем режим работы проекта
defined('DEBUG') or define('DEBUG', true);

// определяем корневую директорию проекта
defined('ROOTDIR') or define('ROOTDIR', __DIR__);
// директория приложения
defined('APPDIR') or define('APPDIR', ROOTDIR . '/app');
// директория фраемворка
defined('FRAEMWORKDIR') or define('FRAEMWORKDIR', ROOTDIR . '/vendor/fraemwork');