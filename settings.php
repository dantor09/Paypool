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
    <?php
        $minset = false;
        $maxset = false;
        if(isset($_POST["Submit"]))
        {
            if(empty($_POST["min"]))
            {
                $minset = true;
                $min = "2021-01-01";
            }
            if(empty($_POST['max'])){
                $maxset = true;
                $max = date('Y-m-d 23:59'); 
            }
            if(!$minset){
                $min = $_POST['min'];
            }
            if(!$maxset){
                $max = $_POST['max']; 
            }

            
            $db = get_mysqli_connection();
            $stmt = $db->prepare("SELECT * FROM Transaction WHERE UserID = ? and PurchaseDate >= ? AND PurchaseDate <= ?");
            $stmt->bind_param('sss',$_SESSION['userid'], $min, $max);
            if($stmt->execute()){    
                echo "Below are the transactions within the given time frame. <br>";
                $result = $stmt->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                echo makeTable($rows);
                $stmt->close();
            }
            else
            {
                echo "Transaction not added<br>";
                echo "Error: " . $stmt->error . "<br>";
            }

            
        }

    ?>

    <br>
    <form method="POST">
       <!-- <label for="item">Item: </label>
        <input type="text" placeholder="Tacos" name = "item"><br> 
        
        there is a default for today, have if statement to default if nothing is entered -->
       
        
        <label>Select date criteria to export your transactions. <br>
        If no time frame was given then all transactions from your profile will be shown. </label><br><hr>
        <label>Start Date:</label>
        <input type="date" id="min" name = "min" ><br>
        <label>End Date:</label>
        <input type="date" id="max" name = "max" >

        <input type="submit" value="Submit" name = "Submit">
        <br>
    </form>

</body>
