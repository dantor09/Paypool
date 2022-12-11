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
    <title>Session <?= $_SESSION['SessionID']?> | <?= $PROJECT_NAME?></title>
    <link rel="stylesheet" href="style.css">

</head>
<body>

    <div class>
        <a href="signin.php"><img src="payool_logo.png" id="logo"/></a>
        <?php
                require_once("nav.php");
        ?>
    </div>
    <h2>
    <?php
        $fname = $_SESSION['fname'];
        $lname = $_SESSION['lname'];
    ?>
    </h2>

    <?php
        $add_user_form = new PhpFormBuilder();
        $add_user_form->set_att("method", "POST");
        $add_user_form->add_input("usermail", array(
            "type" => "submit",
            "value" => "Add by Email"
        ), "email_button");
        $add_user_form-> add_input("Friend's Email", array(
            "type" => "text",
            "placeholder" => "Enter email"
        ), "email_input");
        $add_user_form->build_form();
        
        if(!empty($_POST['email_input']) && isset($_POST['email_button']))
        {
            $db = get_mysqli_connection();
            $query_first_name = $db->prepare("SELECT FName 
            FROM UserProfile 
            WHERE Email = ?");
            $query_first_name->bind_param('s',$_POST['email_input']);
            $query_first_name->execute();
            $result = $query_first_name->get_result();
            $first_name = $result->fetch_all(MYSQLI_ASSOC);
            if(!empty($first_name))
            {
                $db = get_mysqli_connection();
                $already_in_session = $db->prepare("SELECT FName FROM Joins Join UserProfile ON(Joins.UserID = UserProfile.UserID) WHERE SessionID = ?");
                $already_in_session->bind_param('s', $_SESSION['SessionID']);
                $already_in_session->execute();
                $result2 =  $already_in_session->get_result();
                $row_names = $result2->fetch_all(MYSQLI_ASSOC);
                $found = false;
                $index_count = 0;
                while($index_count < count($row_names) && !$found)
                {
                    if($row_names[$index_count]['FName'] == $first_name[0]['FName'])
                    {
                        $found = true;
                    }
                    $index_count++;
                }
                
                if($found)
                {
                    echo "User is already in this paypool session <br>";
                }
                else{
                    $db = get_mysqli_connection();
                    $name = $db->prepare("SELECT UserID FROM UserProfile WHERE FName = ?");
                    $name->bind_param('s', $first_name[0]['FName']);
                    $name->execute();
                    $result = $name->get_result();
                    $user_id = $result->fetch_all(MYSQLI_ASSOC);
                    $query = $db->prepare("CALL AddUserToSession(?,?)");
                    $query->bind_param('ss', $user_id[0]['UserID'], $_SESSION['SessionID']);
                    $query->execute();
                    echo $first_name[0]['FName']. " was added successfully <br>";
                }
                

            }
            else{
                echo "User does not exist <br>";
            }

        }
    ?>

    <?php  
            $db = get_mysqli_connection();
            $query = $db->prepare("CALL GetMembers(?)");
            $query->bind_param('s', $_SESSION['SessionID']);

            if($query->execute())
            {
                $result = $query->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                echo "You have " . count($rows) . " users in session: " . $_SESSION['SessionID'];
                echo makeTable($rows);
                $query->close();
            }
            else
            {
                echo "Error: " . mysqli_error();
                echo "Additinal Error: " . mysqli_errno();
                echo "Even more errors: " . $query->error;
            }
    ?>

    <div class="">
        <?php
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
            $add_transaction_form = new PhpFormBuilder();
            $add_transaction_form->set_att("method", "POST");
            $add_transaction_form->add_input("transaction", array(
                "type" => "submit",
                "value" => "Add Transaction"
            ), "transactionbtn");
            $add_transaction_form-> add_input("PurchaseType", array(
                "type" => "text",
                "placeholder" => "Purchase Type"
            ), "purchase_type_input");
            $add_transaction_form->build_form();    
        ?>

        <h2></h2>
        <?php  
            $db = get_mysqli_connection();
            $query = $db->prepare("CALL GetTransactions(?)");        
            $query->bind_param('s', $_SESSION['SessionID']);

            if($query->execute())
            {
                $result = $query->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                if(count($rows) > 1 || count($rows) == 0){
                    echo "There are " . count($rows) . " transactions";
                }
                else
                { 
                    echo "There is " . count($rows) . " transaction";
                }
                
                echo makeTable($rows);
                $query->close();
            }
            else
            {
                echo "Error: " . mysqli_error();
                echo "Additinal Error: " . mysqli_errno();
                echo "Even more errors: " . $query->error;
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
