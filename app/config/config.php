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
	
];