<?php
// Include this file at the top of any page that requires login
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /factcheck/login.php");
    exit();
}
?>
