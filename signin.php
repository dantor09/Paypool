<?php
	require_once("config.php");
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
        header("Location: dashboard.php");
    }
?>
	
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <img src ="payool_logo.png" />
</head>

<body class="">
    <div class = "">
    <?php

        if (isset($_POST) && !empty($_POST)) {
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);

            $db = get_mysqli_connection();
            $stmt = $db->prepare("SELECT * FROM UserProfile WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            // echo mysql_errno($conn) . ": " . mysql_error($conn) . "\n";
            $data = $result->fetch_assoc();
            if (count($data) > 0){
                if (password_verify($password, $data['Password'])){
                    $_SESSION['logged_in'] = true;
                    $_SESSION['userid'] = $data['UserID'];
                    $_SESSION['fname'] = $data['FName'];
                    $_SESSION['lname'] = $data['LName'];
                    $_SESSION['email'] = $data['Email'];
                    header("Location: dashboard.php");
                } else {
                    $passworderror = true;
                }
            }else{
                $loginerror=true;
            }
            $result->free(); 
        }
       
    ?>
    </div>


<div class = "">
    <?php if(!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false){ ?>
        <div class="">
			<form method="POST" id="">
				Email: <input class = "" type="text" name = "email"/>
				<br>
				Password: <input class = "" type = "password" name = "password"/>
				<br>
				<input class = "signin_btn" type = "submit" value="Log In"/>
			</form>
			<p><a id='signinbtn' href="signup.php">Create a free Paypool Account</a></p>
        </div>
    <?php }
        if($passworderror){
            echo "<br><h3>Incorrect Password. Please enter your password.</h3>";
        }
        IF ($loginerror){
            echo "<br><h3>Please enter a valid email.</h3>";
        }
    ?>
    </div>

</body>
</html>