<nav>
    <div>
        <a href = "index.php">
        <img class="" src = ""/>
        </a>

    </div>

    <ul class = "nav-list">
    <!--If user is signed in show these options -->
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
        <li class = "">
            <a href = "index.php">Home</a>
        </li>

        <li>
            <a href = "index.php">Link 2</a>
        </li>

        <li>
            <a href = "signout.php">Sign Out</a>
        </li>

        <li>
            <a href = "index.php">Signed In</a>
        </li>

    <?php } else { ?>
    <!--If not signed in show these options -->
        <li>
            <a href = "index.php">Home</a>
        </li>

        <li>
            <a href = "signin.php">Sign In</a>
        </li>

        <li>
            <a href = "signup.php">Sign Up</a>
        </li>

        <li>
            <a href = "index.php">Not Signed in</a>
        </li>
    <?php } ?>
    </ul>

</nav>