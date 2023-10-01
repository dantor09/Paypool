<?php
    require_once("config.php");
    if(!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false) { // Redirect to sign in page if NOT logged in
    header("Location: signin.php");
    }
    $db = get_mysqli_connection(); // Establish connection with predefined database (Artemis)

    function isUserInSession($user_id, $session_id) {
        global $db;
        $query = $db->prepare("SELECT UserID FROM Joins WHERE UserID = ? AND SessionID = ?");
        $query->bind_param('ss', $user_id, $session_id);
        $query->execute();
        $result = $query->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $query->close();
        if(count($rows) == 0) {return false;}
        else {return true;}
    }

    function isUserManagerOfSession($user_id, $session_id) {
        global $db;
        $query = $db->prepare("SELECT UserID FROM PaypoolSession WHERE UserID = ? AND SessionID = ?");
        $query->bind_param('ss', $user_id, $session_id);
        $query->execute();
        $result = $query->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $query->close();
        if(count($rows) == 0) {return false;}
        else {return true;}
    }

    function getManagerName($session_id) {
        global $db;
        $query = $db->prepare("SELECT Fname, Lname FROM UserProfile JOIN PaypoolSession ON(UserProfile.UserID = PaypoolSession.UserID) WHERE SessionID = ?");
        $query->bind_param('s', $session_id);
        $query->execute();
        $result = $query->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $query->close();
        return [$rows[0]['Fname'], $rows[0]['Lname']];
    }
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./dashboard.css">
    <title>Dashboard | <?= $PROJECT_NAME?></title>
</head>
<body>
<div>
    <a href="signin.php"><img id="logo" src="payool_logo.png" /></a>
    <?php require_once("nav.php"); ?>                               
</div>


<?php
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
    echo "<h2>Welcome, $fname $lname ! </h2>";
?>

<hr>
<div id="createSessionDiv">
    <?php
        $insert_form = new phpFormBuilder();                        // Create form 
        $insert_form->set_att("method", "POST");                    // Post Method 
        $insert_form->add_input("Insert", array(                    
            "type" => "submit",
            "value" => "Create New Session"                         // Display Create new session on page
        ), "createbtn");                                            // name this button "createbtn"
        $insert_form->build_form();                                 // Build the form 
        if (isset($_POST["createbtn"])) {                             // If button is pressed
            $query = $db->prepare("INSERT INTO PaypoolSession (UserID) VALUES (?)");    // Prepare a query to make new paypool session
            $query->bind_param('s', $_SESSION['userid']);           // Pass the user id of the person logged in, to the query parameter
            
            if ($query->execute()) {header( "Location: " . $_SERVER['PHP_SELF']);}
            else { echo "Error inserting: " . mysqli_error();}
        }
    ?>
</div>
<hr>
<h2>Paypool Sessions</h2>

<div class="sessionContainer">
    <div class="mySessions">
        <div class="inSession"> 
            <?php
            // Query the Session ID's of the Session ID's the user is manager of and the percentage value too 
            $query = $db->prepare("SELECT SessionID AS 'Sessions', Percentage AS 'Percentage' FROM Joins WHERE UserID = ? AND SessionID IN(SELECT SessionID FROM PaypoolSession WHERE UserID = ?)");
            $query->bind_param('ss', $_SESSION['userid'], $_SESSION['userid'] );
            $query->execute();
            $result = $query->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            if($rows == null) {echo "You are not a manager of any sessions";}
            else {
                echo "<h2>Managed</h2>";
                echo makeTable($rows);                      // Make a table and display the information retrieved 
            }
            $query->close();            // Close the query
            ?>
        </div>
        <div class="inSession">
            <?php
            // Query the Session ID's of the Session ID's the user has Joined and is not a manager of ... 
            $query = $db->prepare("SELECT SessionID AS 'Joined Sessions\t', Percentage FROM Joins WHERE UserID = ? AND SessionID NOT IN(SELECT SessionID FROM PaypoolSession WHERE UserID = ?)");
            $query->bind_param('ss', $_SESSION['userid'], $_SESSION['userid'] );
            $query->execute();
            $result = $query->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            if($rows == null) {echo "You have not joined any sessions";}
            else {
                echo "<h2>Joined</h2>";
                echo makeTable($rows);                      // Make a table and display the information retrieved
            }
            $query->close();            // Close the query
            ?>
        </div>
    </div>
    <div id="enterSession">
        <?php 
        //Build form/button to enter a specified session  
        $session_form = new phpFormBuilder();
        $session_form->set_att("method", "POST");
        $session_form->add_input("", array(
            "type" => "text",
            "placeholder" => "Enter a session ID",
            "class" => "sessionInput",
        ), "enter_session");
        $session_form->add_input("Session", array(
            "type" => "submit",
            "value" => "Enter Session",
            "class" => "sessionbtn"
        ), "sessionbtn");
        $session_form->build_form();
        if(!empty($_POST['enter_session'])) {
            $user_found_in_session = isUserInSession($_SESSION['userid'], $_POST['enter_session']);
            if($user_found_in_session) { // check if user manager of the session
                $user_is_manager = isUserManagerOfSession($_SESSION['userid'], $_POST['enter_session']);
                
                // getManagerName returns an array of the manager's first and last name
                $name = getManagerName($_POST['enter_session']);
                $_SESSION['manager_first_name'] = $name[0];
                $_SESSION['manager_last_name'] = $name[1];
                
                if($user_is_manager) {
                    header("Location: manager_session.php");
                }
                else {
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
</body>
</html>
