<?php
	require_once("config.php");
?>
	
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | <?= $PROJECT_NAME?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="">
	<h1><?= $PROJECT_NAME?></h1>
	<div class="nav">
		<?php
			require("nav.php");
		?>
	</div>
	
    <div class = "">
    <?php
        //$passworderror = FALSE;
        //$loginerror = false;
        //uncomment when finished setting up connect.php database side
        if (isset($_POST) && !empty($_POST)) {
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);

            
            $db = get_mysqli_connection();
            $stmt = $db->prepare("SELECT * FROM UserProfile WHERE Email = ? limit 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            // echo mysql_errno($conn) . ": " . mysql_error($conn) . "\n";
            $data = $result->fetch_assoc();
            if (count($data) > 0){
                if (password_verify($password, $data['Password'])){
                    $_SESSION['logged_in'] = true;
                    $_SESSION['userid'] = $data['UserID'];
                    $_SESSION['name'] = $data['FName'];
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
			<h2>Welcome to the Sign In/Sign Up page</h2>
			<form method="POST" id="">
				Email: <input class = "" type="text" name = "email"/>
				<br>
				Password: <input class = "" type = "password" name = "password"/>
				<br>
				<input class = "signin_btn" type = "submit" value="login"/>
			</form>
			<p>Don't have an account? <a id='signinbtn' href="signup.php">Sign up</a></p>
        </div>
    <?php } else{ ?>
        <h1>You are logged in</h1>
    <?php }
        if($passworderror){
            echo "<br><h3>INCORRECT PASSWORD</h3>";
        }
        IF ($loginerror){
            echo "<br><h3>Email does not exist; verify your spelling.</h3>";
        }
    ?>
    </div>

</body>
</html>