<!-- includes/auth.php -->

<?php
session_start();

require_once 'db.php';
require_once 'functions.php';

function require_login() {
    if (!isset($_SESSION['user_email'])) {
        redirect('/public/login.php');
    }
}

function is_admin() {
    return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'super_admin']);
}
?>
