<?php

$this->addScript('/assets/vendor/jquery/jquery-1.12.1.min.js');
$this->addScript('/assets/vendor/angular/angular.min.js');
$this->addScript('/assets/vendor/bootstrap/js/bootstrap.min.js');
$this->addStyle('/assets/vendor/bootstrap/css/bootstrap.min.css');
$this->addScript('/assets/js/store.js');

?>

<!DOCTYPE html>
<html ng-app="store">
	<head>
		<meta charset="UTF-8">
		<title><?php echo $this->place('title')?></title>
		<?php echo $this->styles()?>
		<?php echo $this->scripts()?>
	</head>
	<body>
	   	<div><?php echo $this->insert('includes/header')?></div>
	   	<div><?php echo $this->place('content')?></div>
	   	<div><?php echo $this->insert('includes/footer')?></div>
	</body>
</html>