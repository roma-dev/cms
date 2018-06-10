<!doctype html>
<html>
	<head>
		<title><?= isset($title)? $title : 'Title';?></title>
	</head>
	<body>
		<h1><?=isset($title)? $title : '';?></h1>
		<?= $content ?>
	</body>
</html>