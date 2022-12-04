<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <style>
    </style>
</head>

<body class="">
    <div class = "">
    <?php
        //$passworderror = FALSE;
        //$loginerror = false;
        //uncomment when finished setting up connect.php database side
        require_once "config.php";
        require "nav.php";
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
                    header("Location: index.php");
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


<div class = >
    <?php if(!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false){ ?>
        <div class="">
        <form method="POST" id="">
        <h2>Welcome to the Sign In/Sign Up page</h2>
        Email: <input class = "" type="text" name = "email"/>
        <br>
        Password: <input class = "" type = "password" name = "password"/>
        <br>
        <input class = "signin_btn" type = "submit" value="login"/>
        <p>Don't have an account? <a id='signinbtn' href="signup.php">Sign up</a></p>
        </form>
        </div>
    <?php } else{ ?>
        <h1>You are logged in</h1>
    <?php }
        if($passworderror){
            echo "<br><h2>INCORRECT PASSWORD</h2>";
        }
        IF ($loginerror){
            echo "<br><h2>Email Doesn't exist, verify your spelling.</h2>";
        }
    ?>
    </div>

</body>
</html>