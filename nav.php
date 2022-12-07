<nav>
	<?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
	    <!--If user is signed in show these options -->
            <a href = "dashboard.php">Dashboard</a>
            <a href = "index.php">Settings</a>
            <a href = "signout.php">Sign Out</a>
            <p>[Signed In]</p>
    <?php } else { ?>
		<!--If not signed in show these options -->
            <a href = "standardSession.php">Home</a>
            <a href = "signin.php">Sign In</a>
            <a href = "signup.php">Sign Up</a>
            <p>[Not Signed In]</p>
    <?php } ?>
</nav>