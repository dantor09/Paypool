<?php
require_once("config.php");
if(!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false){
    header("Location: signin.php");
}
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Profile Settings | <?= $PROJECT_NAME ?></title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    
	<div class>
        <a href="signin.php"><img src="payool_logo.png" id="logo"/></a>
		<?php require_once "nav.php";?>
    </div>

</body>
