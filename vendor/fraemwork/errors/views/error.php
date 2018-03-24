<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="/public/favicon.ico" type="image/x-icon">
    <title>Ошибка</title>
	<link rel="stylesheet" type="text/css" href="/public/css/bootstrap.min.css">
	<style>
		
		.bs-callout {
			margin: 20px 0;
			padding: 10px 20px 20px 20px;
			border-left: 3px solid #eee;
		}
		.bs-callout-danger {
			background-color: #fdf7f7;
			border-color: #d9534f;
		}
		.bs-callout-danger h3{
			margin-bottom: 20px;
		}
	</style>
		
</head>
<body>
	
	<div class="container">
		<div class="bs-callout bs-callout-danger">
			<h3 class="text-danger">ПРОИЗОШЛА ОШИБКА: <?=$this->errors[$errno]?></h3>
			<h4>Ошибка: <?=$errstr?></h4>
			<h4>В файле: <code><?=$errfile?></code></h4>
			<h4>На строке: <?=$errline?></h4>
		</div>
	</div>

	<script type="text/javascript" src="/public/js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
</body>
</html>