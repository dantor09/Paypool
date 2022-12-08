<nav>
	<?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
	    <!--If user is signed in show these options -->
            <a href = "dashboard.php">Dashboard</a>
            <a href = "settings.php">Settings</a>
            <a href = "signout.php">Sign Out</a>
    <?php } else { ?>
		<!--If not signed in show these options -->
            <a href = "dashboard.php">Home</a>
            <a href = "signin.php">Sign In</a>
            <a href = "signup.php">Sign Up</a>
    <?php } ?>
</nav>