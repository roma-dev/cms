<!doctype html>
<html>
	<head>
		<title><?= isset($title)? $title : 'Title';?></title>
		<?= $this->blockHead();?>
	</head>
	<body>
		<h1><?=isset($title)? $title : '';?></h1>
		<?= $content ?>
		<?= $this->blockFooter(); ?>
	</body>
</html>