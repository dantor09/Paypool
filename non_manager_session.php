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
            
        $db = get_mysqli_connection();
        $query4 = $db->prepare("CALL GetMembers(?)");
        $query4->bind_param('s', $_SESSION['SessionID']);
        if($query4->execute())
        {
            $result4 = $query4->get_result();
            $rows4 = $result4->fetch_all(MYSQLI_ASSOC);
            
            if(count($rows4) > 1 || count($rows4) == 0){
                echo "There are " . count($rows4) . " members in session " . $_SESSION['SessionID'];
            }
            else
            { 
                echo "There is " . count($rows4) . " member in session " . $_SESSION['SessionID'];
            }
            echo makeTable($rows4);
            $query4->close();
        }
        else
        {
            echo "Error: " . mysqli_error();
            echo "Additinal Error: " . mysqli_errno();
            echo "Even more errors: " . $query4->error;
        }
    ?>

    <form method="POST">
        <label for="cat">Transaction Category:</label>
        <select name="cat" id="cat">
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
        <input type="text" name = "item"><br> 

        <label for="price">Price: </label>
        <input type="text" name = "price"><br>


        <label>Date of Transaction: </label>
        <input type="datetime-local" id="test_datetimelocal"><br>

        <input type="submit" value="Submit">
        <br><br>
    </form>


    <?php
    /*
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
        */
    ?>

<br><br>
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
        }
        else
        {
            echo "Error: " . mysqli_error();
            echo "Additinal Error: " . mysqli_errno();
            echo "Even more errors: " . $query->error;
        }
    ?>  
   
    
</body>
</html>
