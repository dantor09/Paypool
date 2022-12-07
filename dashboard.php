<?php
require_once("config.php");
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard | <?= $PROJECT_NAME ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?= $PROJECT_NAME?></h1>

    <div class = "nav">
    	<?php
        	require_once("nav.php");
    	?>
    </div>

    <h4>Welcome [first name using php]!</h4>

    <div class="join">
	<h2>Join a Session</h2>

	<?php
		//php logic goes here to create form to join a session
	?>
    <div>

    <div class="">
    	<h2>My Sessions</h2>
	
	<?php
		//php logic to populate with current logged in user sessions
	?>
    </div>

</body>
</html>
