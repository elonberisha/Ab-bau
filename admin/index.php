<?php
require_once 'functions.php';

// If logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
} else {
    // If not logged in, redirect to login
    header('Location: login.php');
    exit;
}
?>

