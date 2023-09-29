<?php
    require_once("config.php");
    if(!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false) { // Redirect to sign in page if NOT logged in
    header("Location: signin.php");
    }
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <title>Dashboard | <?= $PROJECT_NAME?></title>
</head>
<body>
    <main>
    <div>
        <a href="signin.php"><img id="logo" src="payool_logo.png" /></a>
        <?php require_once("nav.php"); ?>                               
    </div>

    <h2>
        <?php
            $fname = $_SESSION['fname'];
            $lname = $_SESSION['lname'];
            echo "Welcome, $fname $lname !";
        ?>
    </h2>
    <hr>
    <div id="createSessionDiv">
        <?php
            $insert_form = new PhpFormBuilder();                        // Create form 
            $insert_form->set_att("method", "POST");                    // Post Method 
            $insert_form->add_input("Insert", array(                    
                "type" => "submit",
                "value" => "Create New Session"                         // Display Create new session on page
            ), "createbtn");                                            // name this button "createbtn"

            $insert_form->build_form();                                 // Build the form 

            if (isset($_POST["createbtn"])) {                             // If button is pressed
                $db = get_mysqli_connection();                          // Establish connection with predefined database (Artemis)
                $query = $db->prepare("INSERT INTO PaypoolSession (UserID) VALUES (?)");    // Prepare a query to make new paypool session
                $query->bind_param('s', $_SESSION['userid']);           // Pass the user id of the person logged in, to the query parameter
                
                if ($query->execute()) {                                // Execute the query on the database command line    
                    header( "Location: " . $_SERVER['PHP_SELF']);       // Refresh the page 
                }
                else {
                    echo "Error inserting: " . mysqli_error();          // Display error if execution of query failed 
                }
            }
        ?>
    </div>
    <hr>
    <h2>My paypool sessions</h2>

    <div class="sessionContainer">
        <div class="mySessions">
            <div class="inSession"> 

                <?php
                // Query the Session ID's of the Session ID's the user is manager of and the percentage value too 
                $db = get_mysqli_connection();
                $query = $db->prepare("SELECT SessionID AS 'Managed Sessions\t', Percentage AS 'Percentage' FROM Joins WHERE UserID = ? AND SessionID IN (SELECT SessionID FROM PaypoolSession WHERE UserID = ?)");
                $query->bind_param('ss', $_SESSION['userid'], $_SESSION['userid'] );
                $query->execute();
                $result = $query->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                echo makeTable($rows);                      // Make a table and display the information retrieved 
                ?>
            </div>

            <div class="inSession">
                <?php
                // Query the Session ID's of the Session ID's the user has Joined and is not a manager of ... 
                $db = get_mysqli_connection();
                $query = $db->prepare("SELECT SessionID AS 'Joined Sessions\t', Percentage FROM Joins WHERE UserID = ? AND SessionID NOT IN (SELECT SessionID FROM PaypoolSession WHERE UserID = ?)");
                $query->bind_param('ss', $_SESSION['userid'], $_SESSION['userid'] );
                $query->execute();
                $result = $query->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                echo makeTable($rows);
                $query->close();            // Close the query
                ?>
            </div>
        </div>

        <div id="enterSession">
            <?php 

            //Build form/button to enter a specified session  
            $session_form = new phpFormBuilder();
            $session_form->set_att("method", "POST");
            $session_form->add_input("Session number:", array(
                "type" => "text",
                "placeholder" => "Enter a session ID",
                "class" => "other"
            ), "enter_session");
            $session_form->add_input("Session", array(
                "type" => "submit",
                "value" => "Enter Session"
            ), "sessionbtn");
            $session_form->build_form();

            if(!empty($_POST['enter_session'])) {
                // Query the User ID's of the people in a certain session via the session ID
                $db = get_mysqli_connection();
                $query = $db->prepare("SELECT UserID FROM Joins WHERE SessionID = ? ");
                $query->bind_param('s', $_POST['enter_session']);
                $query->execute();
                $result = $query->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $index_count = 0;
                $found = false;

                // Iterate through the results .. in this case the User ID's 
                // while there are still ID's left and have not found our target
                while($index_count < count($rows) && !$found) {
                    if($rows[$index_count]['UserID'] == $_SESSION['userid']) { // if person that is currently logged in is found in response
                        $found = true;                                         // then set $found boolean flag to true 
                    }
                    $index_count++;                                            // Iterate through next user id
                }
                $query->close();
                //if user is in session then check if they manage it
                if($found) {
                    $db = get_mysqli_connection();
                    // Check that User Manages Session Entered 
                    $query = $db->prepare("SELECT Fname, Lname, PaypoolSession.UserID FROM PaypoolSession JOIN UserProfile ON(UserProfile.UserID = PaypoolSession.UserID) WHERE SessionID = ? ");
                    $query->bind_param('s', $_POST['enter_session']);
                    $query->execute();
                    $result = $query->get_result();
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                    
                    // Set the Super global variable manager_first_name to the first name
                    // Set the Super global variable maneger_last_name to the last name
                    $_SESSION['manager_first_name'] = $rows[0]['Fname'];
                    $_SESSION['manager_last_name'] = $rows[0]['Lname'];
                    
                    //if user is manager then redirect to manager session page
                    if($rows[0]['UserID'] == $_SESSION['userid']) {
                        $_SESSION['SessionID'] = $_POST['enter_session'];
                        header("Location: manager_session.php");
                    }
                    else { // redirect to non manager session view
                    
                        $_SESSION['SessionID'] = $_POST['enter_session'];
                        header("Location: non_manager_session.php");
                    }
                }
                else {
                    echo "You are not in session " . $_POST['enter_session'];
                }    
            }
            ?>
        </div>
    </div>
        </main>
</body>
</html>
