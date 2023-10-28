<?php
    require_once("config.php");
    if(!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false) {
    header("Location: signin.php");
    }
    if($_SESSION['is_manager'] == false) {
        header("Location: dashboard.php");
    }
    
    $db = get_mysqli_connection();
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title><?= $_SESSION['GroupName']?> | <?= $PROJECT_NAME?></title>
    <link rel="stylesheet" href="./assets/css/manager_session.css">

</head>
<body>
    <?php 
        function display_session_text($users_in_session_count) {
            $text = "You have " . $users_in_session_count;
            $text .= ($users_in_session_count > 1) ? " users in session: " : " user in session: ";
            $text .= $_SESSION['SessionID'];

            return $text;
        }
    ?>
    <div class="logo_container">
        <a href="signin.php"><img id="logo" src="credit_card.png" /></a>
        <h1 id="paypool_text">PayPool</h1>
    </div>
    <?php require_once("nav.php");?>
    <?php echo "<h2>Group Name: " .$_SESSION['GroupName'] . "</h2>"?>
    <h2> You are the manager of this session </h2> 
    
    <?php
        if(!empty($_POST['email_input']) && isset($_POST['email_button'])) {
            $query_incoming_user = $db->prepare("SELECT UserID, FName, Email
            FROM UserProfile 
            WHERE Email = ?");
            $query_incoming_user->bind_param('s',$_POST['email_input']);
            $query_incoming_user->execute();
            $result = $query_incoming_user->get_result();
            $incoming_user = $result->fetch_all(MYSQLI_ASSOC);

            if(!empty($incoming_user)) {
                $already_in_session = $db->prepare("SELECT FName, Email FROM Joins Join UserProfile ON(Joins.UserID = UserProfile.UserID) WHERE SessionID = ?");
                $already_in_session->bind_param('s', $_SESSION['SessionID']);
                $already_in_session->execute();
                $result2 =  $already_in_session->get_result();
                $row_email = $result2->fetch_all(MYSQLI_ASSOC);
                $found = false;
                $index_count = 0;
                //while statement to make add everyone listed
                while($index_count < count($row_email) && !$found) {
                    if($row_email[$index_count]['Email'] == $incoming_user[0]['Email']) {
                        $found = true;
                    }
                    $index_count++;
                }
                
                //check to see if user is in the session already
                if($found) {
                    echo "User is already in this paypool session <br>";
                }
                else {     
                    $query = $db->prepare("CALL AddUserToSession(?,?)");
                    $query->bind_param('ss', $incoming_user[0]['UserID'], $_SESSION['SessionID']);
                    $query->execute();
                    echo $incoming_user[0]['FName']. " was added successfully <br>";
                    $query->close();
                }
            }   
            else {
                echo "User does not exist<br>";
            }
        }
        ?>
<div class="paypool_container">
    <?php        
        $add_user_form = new PhpFormBuilder();
        $add_user_form->set_att("method", "POST");
    ?>
    <div class="email_section">
    <?php 
        $add_user_form-> add_input("", array(
            "type" => "text",
            "placeholder" => "Enter email"
        ), "email_input");
        $add_user_form->add_input("usermail", array(
            "type" => "submit",
            "value" => "Add User"
        ), "email_button");
        $add_user_form->build_form();
        
        $update_percentage = $db->prepare("CALL UpdatePercentages(?)");
        $update_percentage->bind_param('s', $_SESSION['SessionID']);
        $update_percentage->execute();
        $update_percentage->close();
    ?>
    </div>
    <?php  
        $query = $db->prepare("CALL GetMembers(?)");
        $query->bind_param('s', $_SESSION['SessionID']);
        if($query->execute()) {
            $result = $query->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            echo "<div class='users_in_session'>" . display_session_text(count($rows)) . "</div>";
            echo "<div class='members_table text_output'>" . makeTable($rows) . "</div>";
            $query->close();
            echo "<br>";
        }
        else {
            echo "Error: " . mysqli_error();
            echo "Additinal Error: " . mysqli_errno();
            echo "Even more errors: " . $query->error;
        }
    ?>

    <form  class="transaction_section" method="POST">
        <label for="category">Transaction Category:</label>
        <select name="category" id="category">
            <option value="food">Food</option>
            <option value="gas">Gas</option>
            <option value="transportation">Transportation</option>
            <option value="hotel">Hotel</option>
            <option value="bars">Bars</option>
            <option value="entertainment">Entertainment</option>
            <option value="electronics">Electronics</option>
            <option value="other">Other</option>
        </select>
        <br>

        <label for="item">Item: </label>
        <input type="text" placeholder="Tacos" name = "item"><br> 
        
        <label for="price">Price: </label>
        <input type="text" placeholder = "4.00" name = "price"><br>
    
        <!--there is a default for today, have if statement to default if nothing is entered -->
        <label>Date of Transaction: </label>
        <input type="datetime-local" id="test_datetimelocal" name = "date" ><br>

        <input type="submit" value="Create PayPool transaction" name = "Submit">
        <br>
    </form>

    <?php
        if(isset($_POST["Submit"]) && !empty($_POST["item"]) && !empty($_POST["price"])) {
            if(!$_POST["date"]) {
                date_default_timezone_set('America/Los_Angleles');
                $_POST["date"] = date('Y-m-d H:i');
            }
        
            $stmt = $db->prepare("CALL AddTransaction (?,?,?,?,?,?)");
            $stmt->bind_param('sssssd',$_SESSION['userid'], $_SESSION['SessionID'], $_POST['category'], $_POST["date"],$_POST["item"], $_POST["price"]);
            if($stmt->execute()) {    
                echo "<p>Transaction added successfully.</p>";
                $stmt->close();
            }
            else {
                echo "Transaction not added<br>";
                echo "Error: " . $stmt->error . "<br>";
            }
        }
    ?>
    <?php
        $remove_transaction_form = new PhpFormBuilder();
        $remove_transaction_form->set_att("method", "POST");
    ?>
    <div class="remove_transaction_section">
    <?php 
        $remove_transaction_form-> add_input("", array(
            "type" => "text",
            "placeholder" => "9"
        ), "transaction_input");
        $remove_transaction_form->add_input("transaction", array(
            "type" => "submit",
            "value" => "Remove Transaction"
        ), "remove_transaction_button");
        
        $remove_transaction_form->build_form();
        
        if(!empty($_POST['transaction_input']) && isset($_POST['remove_transaction_button'])) {
            $remove_transaction = $db->prepare("CALL RemoveTransaction(?,?)");
            $remove_transaction->bind_param('ss',$_POST['transaction_input'], $_SESSION['SessionID']);
            $remove_transaction->execute();
        }
    ?>
    </div> 
     <?php  
        $query = $db->prepare("CALL GetTransactions(?)");        
        $query->bind_param('s', $_SESSION['SessionID']);
        if($query->execute()) {
            $result = $query->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            if(count($rows) > 1 || count($rows) == 0) {
                echo "<div class='text_output plural_transaction'> " .count($rows) . " transactions </div>";
            }
            else { 
                echo "<div class='text_output single_transaction'>" . count($rows) . " transaction </div>";
            }
            echo "<div class='transactions_table text_output'>" . makeTable($rows) . "</div>";
            $query->close();
        }
        else {
            echo "Error: " . mysqli_error();
            echo "Additinal Error: " . mysqli_errno();
            echo "Even more errors: " . $query->error;
        }
        ?>

        <?php
        $delete_form = new PhpFormBuilder();
        $delete_form->set_att("method", "POST");
        ?>
        <div class="delete_paypool_section">
        <?php 
        $delete_form->add_input("Insert", array(
            "type" => "submit",
            "value" => "Delete this Paypool Session - WARNING: IRREVERSIBLE"
        ), "delete_id");
        $delete_form->build_form();
        ?>
        </div>
        <?php
        if (isset($_POST["delete_id"])) {
            $query2 = $db->prepare("DELETE FROM Joins WHERE SessionID = (?)");
            $query2->bind_param("s", $_SESSION['SessionID']);
            
            if($query2->execute()) {
                $query2->close();
                $_SESSION["affected_rows"] = $db->affected_rows;
            } 
            else {
                echo "Error: " . mysqli_error();
                echo "Additinal Error: " . mysqli_errno();
                echo "Even more errors: " . $query2->error;
            }

            $query3 = $db->prepare("DELETE FROM Transaction WHERE sessionId = ?");
            $query3->bind_param("s", $_SESSION['SessionID']);
            if($query3->execute()) {
                $query3->close();
                $_SESSION['affected_rows'] = $db->affected_rows;
            } 
            else {
                echo "Error: " . mysqli_error();
                echo "Additinal Error: " . mysqli_errno();
                echo "Even more errors: " . $query3->error;
            }

            $query1 = $db->prepare("DELETE FROM PaypoolSession where sessionId = ?");
            $query1->bind_param("s", $_SESSION['SessionID']);
            if($query1->execute()) {
                $query1->close();
                $_SESSION["affected_rows"] = $db->affected_rows;
                $_SESSION['SessionID'] = NULL;
                header("Location: dashboard.php");
            } 
            else {
                echo "Error: " . mysqli_error();
                echo "Additinal Error: " . mysqli_errno();
                echo "Even more errors: " . $query1->error;
            }
        }
    ?> 
    
    <?php  
        $totalquery = $db->prepare("SELECT SUM((Transaction.Price)* ((Joins.Percentage)/100)) AS 'Total'  
            FROM Joins 
            JOIN Transaction ON(Transaction.UserID = Joins.UserID)  
            WHERE Joins.SessionID = ? AND  Transaction.SessionID = ?");
        $totalquery->bind_param("ss", $_SESSION['SessionID'], $_SESSION['SessionID']);

        if($totalquery->execute()) {
            $result = $totalquery->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $totaldue = number_format($data[0]['Total'],2);
            $totalquery->close();
        } 
        else {
            echo "Error: " . mysqli_error();
            echo "Additinal Error: " . mysqli_errno();
            echo "Even more errors: " . $totalquery->error;
        }

        $totalquery2 = $db->prepare("SELECT SUM(Price) AS 'Total'  
            FROM Transaction
            WHERE SessionID = ?");
        $totalquery2->bind_param("s", $_SESSION['SessionID']);

        if($totalquery2->execute()) {
            $result = $totalquery2->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $totaldue2 = number_format($data[0]['Total'],2);
            $totalquery2->close();
        } 
        else {
            echo "Error: " . mysqli_error();
            echo "Additinal Error: " . mysqli_errno();
            echo "Even more errors: " . $totalquery2->error;
        }
        echo "<h2 class='session_total'>Session Transaction Total: $"; 
        echo $totaldue2;
        echo "</h2>";
    ?>
    </div>
    <?php 
        
        echo "<h2>Total Each Member Owes to Session: $"; 
        echo $totaldue;
        echo"</h2>";
        $db->close();
    ?>
</body>
</html>
