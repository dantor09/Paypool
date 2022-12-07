<?php
require_once("config.php");
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $PROJECT_NAME ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
	<h1><?= $PROJECT_NAME?></h1>
    
	<div class = "nav">
		<?php
			require_once "nav.php";
		?>
    </div>

</body>
