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
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
    <a href = "dashboard.php"><img src ="payool_logo.png" /></a>

</head>
<body>

    <div class = "nav">
        <?php require_once("nav.php");?>
    </div>
    <h2>
    <?php
        $fname = $_SESSION['fname'];
        $lname = $_SESSION['lname'];
        // -----------------    New Edits   -------------------------------------------
        echo "Welcome $fname $lname!"
        ?>
    </h2>

        <h2>Create a Session</h2>

    <?php
        $insert_form = new PhpFormBuilder();
        $insert_form->set_att("method", "POST");
        $insert_form->add_input("Insert", array(
            "type" => "submit",
            "value" => "Create New Group"
        ), "createbtn");
        
        $insert_form->build_form();
        if (isset($_POST["createbtn"])) {
            $db = get_mysqli_connection();
            $query = $db->prepare("insert into PaypoolSession (UserID) values (?)");
            $query->bind_param('s', $_SESSION['userid']);
            $flag = 0;
            if ($query->execute()) {
                $flag = 1;
            }
            if ($flag) {    
                header( "Location: " . $_SERVER['PHP_SELF']);
            }
            else {
                echo "Error inserting: " . mysqli_error();
            }
        }

                //php logic goes here to create form to join a session
        ?>


    <div class="">
        <h2>My Sessions</h2>

        <?php
            $db = get_mysqli_connection();
            $query = $db->prepare("SELECT SessionID AS 'Managed Sessions\t', Percentage AS 'Percentage' FROM Joins WHERE UserID = ? AND SessionID IN (SELECT SessionID FROM PaypoolSession WHERE UserID = ?)");
            $query->bind_param('ss', $_SESSION['userid'], $_SESSION['userid'] );
            $query->execute();
            $result = $query->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            echo makeTable($rows);

    //DELETE A SESSION THAT USER MANAGES
    //if (!$query->get_result()) {
    //    $delete_form = new PhpFormBuilder();
    //    $delete_form->set_att("method", "POST");
    //    $delete_form->add_input("Enter Session ID to Delete:", array(
    //        "type" => "number",
    //    ), "delete_id");
    //    $delete_form->build_form();
    //    if (isset($_POST["delete_id"])) {
    //        $db = get_mysqli_connection();
    //        //$query1 = false;
    //        //$query2 = false;
//
    //        if (!empty($_POST["delete_id"])) {
    //            echo "deleting by id...";
    //            $query1 = $db->prepare("delete from PaypoolSession where SessionID = ?");
    //            $query1->$bind_param("s", $_POST['delete_id']);
    //            $query1->execute();
    //            $_SESSION["affected_rows"] = $db->affected_rows;
    //            $query2 = $db->prepare("delete from Joins where SessionID = ?");
    //            $query2->$bind_param("s", $_POST['delete_id']);
    //            $query2->execute();
    //            $_SESSION["affected_rows"] = $db->affected_rows;
    //            //header("Location: " . $_SERVER["PHP_SELF"]);
    //        }
    //    }
    //}
    ?>
    
    <?php 
        //php to display certain users 
        $session_form = new phpFormBuilder();
        $session_form->set_att("method","POST");
        $session_form->add_input("Session", array(
            "type" => "submit",
            "value" => "Enter Session"
        ), "sessionbtn");
        $session_form->add_input("Session to enter", array(
            "type" => "text",
            "placeholder" => "Enter a session ID to enter"
        ), "enter_session");
        $session_form->build_form();

        if(!empty($_POST['enter_session']))
        {
            echo "Connecting to session...";
            $db = get_mysqli_connection();
            // Check that User Manages Session Entered 
            $query = $db->prepare("SELECT UserID FROM PaypoolSession WHERE SessionID = ? ");
            $query->bind_param('s', $_POST['enter_session']);
            $query->execute();
            $result = $query->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            if($rows[0]['UserID'] == $_SESSION['userid'])
            {
                $_SESSION['SessionID'] = $_POST['enter_session'];
                header("Location: manager_session.php");
            }
            else{
                echo "You are not in this session!";
            }
            
            echo "You have " . count($rows) . " users in your session.";
                
            echo makeTable($rows);
        }
    
    ?>
    <h2></h2>
    <?php
    if (!$query->get_result()) {
        echo "Transactions from Sessions that you Manage:";
        $query2 = $db->prepare("SELECT TransactionID, concat(FName,' ',LName) AS 'Member', SessionID, ItemPurchased, Price FROM Transaction NATURAL JOIN UserProfile WHERE SessionID IN (SELECT SessionID FROM PaypoolSession WHERE UserID = ?)");  
        $query2->bind_param('s', $_SESSION['userid']);
        $query2->execute();
        $result2 = $query2->get_result();
        $rows2 = $result2->fetch_all(MYSQLI_ASSOC);
        echo makeTable($rows2);
    }
    ?>
    <h2></h2><h2></h2>
    <?php
        $db = get_mysqli_connection();
        $query = $db->prepare("SELECT SessionID AS 'Joined Sessions\t', Percentage FROM Joins WHERE UserID = ? AND SessionID NOT IN (SELECT SessionID FROM PaypoolSession WHERE UserID = ?)");
        $query->bind_param('ss', $_SESSION['userid'], $_SESSION['userid'] );
        $query->execute();
        $result = $query->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        echo makeTable($rows);
        
        if (!$query->get_result()) {
            echo "Transactions from Joined Sessions:";
            $query2 = $db->prepare("SELECT TransactionID, concat(FName,' ',LName) AS 'Member', SessionID, ItemPurchased, Price FROM Transaction NATURAL JOIN UserProfile WHERE SessionID IN (SELECT SessionID FROM Joins wherE UserID = ?) AND SessionID NOT IN (SELECT SessionID FROM PaypoolSession WHERE UserID = ?)");  
            $query2->bind_param('ss', $_SESSION['userid'], $_SESSION['userid']);
            $query2->execute();
            $result2 = $query2->get_result();
            $rows2 = $result2->fetch_all(MYSQLI_ASSOC);
            echo makeTable($rows2);
        }
                //php logic to populate with current logged in user sessions
        ?>
    </div>

</body>
</html>
