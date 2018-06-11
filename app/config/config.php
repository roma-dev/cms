<?php

return [
	
	'errorHandler' => // настройки обработчика ошибок
	[
		'logs' => false, // будем логировать ошибки или нет
		'logFiles' => // в какие файлы и при каких ошибках
		[
			'403' => '/logs/forbbiden.log',
			'404' => '/logs/notfound.log',
			'500' => '/logs/errors.log',
		],
		'errorsView' => // файлы видов отображения ошибок для фронтенда
		[
			'403' => '/views/errors/e403.php',
			'404' => '/views/errors/e404.php',
			'500' => '/views/errors/e500.php',
			'503' => '/views/errors/e503.php',
		]
	],
	
	'db' => 
	[
		'driver'	=> 'mysql',
		'host'		=> '127.0.0.1',
        'port'      => '3306',
		'dbname'	=> 'pdotest',
		'user'		=> 'root',
		'password'	=> '123456',
		'charset'	=> 'utf8',
		'options'	=> 
		[
			PDO::ATTR_PERSISTENT	     => false,                  // отключаем сохранение постоянного соединения
			PDO::ATTR_ERRMODE		     => PDO::ERRMODE_EXCEPTION, // включаем режим при котором PDO будет выбрасывать все исключения
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC        // выборка будет происходит в виде асс. массива
		],
	],
	
	'autoloadListFolder' => 
	[
		'app' => '/',
		'core' => '/vendor/',
		'libs' => '/vendor/',
	],
	'routes' => 
	[
		'/' => ['controller' => 'main', 'action' => 'index'],
		'/contacts' => ['controller' => 'main', 'action' => 'contacts'],
	],
    'controllersNamespace' => 'app\\controllers\\',
	'defaultLayout' => 'default',
	'defaultDirectoryCss' => 'public/css',
	'defaultDirectoryJs' => 'public/js',
	
];