<?php
require_once("config.php");
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $PROJECT_NAME ?></title>
    <link rel="stylesheet" href="style.css">
    <img src ="payool_logo.png" />

</head>
<body>
    
	<div class = "nav">
		<?php
            if(!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false){
                header("Location: signin.php");
            }
			require_once "nav.php";
		?>
    </div>

</body>
