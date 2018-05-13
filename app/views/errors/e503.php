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
			background-color: #f7fafd;
			border-color: #659fbf;
		}
		.bs-callout-danger h3{
			margin-bottom: 20px;
		}
	</style>
		
</head>
<body>
	
	<div class="container">
		<div class="bs-callout bs-callout-danger">
			<h3 class="text-info"><?=$errstr?></h3>
		</div>
	</div>

	<script type="text/javascript" src="/public/js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
</body>
</html>