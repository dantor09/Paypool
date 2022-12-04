<?php
require_once "config.php";
if (!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false) {
    header("Location: signin.php");
}
session_unset();
session_destroy();
header("Location: index.php");
$conn->close();
?>