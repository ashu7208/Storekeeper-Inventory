<?php
// logout.php - Logout functionality
session_start();
session_destroy();
header('Location: login.php');
exit();
?>