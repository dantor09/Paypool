<?php
require_once("config.php");
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Session # | My Sessions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?= $PROJECT_NAME?></h1>

    <div class = "nav">
    	<?php
        	require_once("nav.php");
    	?>
    </div>

    <div class="session">
	<h2>Session [session num using php]</h2>

	<!-- button will possibly require php to trigger deleting session from individual in datbase -->
	<button type="button">Leave Session</button>
    </div>

    <div class="sessionMems">
	<h3>Session Members</h3>

	<?php
		//php logic goes here to view populated table of members in current session
	?>
    <div>

    <div class="addTrans">
    	<h3>Add Transaction</h3>
	
	<?php
		//php logic to add a transaction to session transactions
	?>
    </div>

   <div class="transactions">
   	<h3>All Transactions</h3>
	
	<?php
		//php logic to show all transactions of current session
	?>
   </div>

   <div class="transactions">
	<h3>My Transactions</h3>
	
	<?php
		//php logic to show current user's transactions & ability to delete
	?>
   
   </div>


</body>
</html>
