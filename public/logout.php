<?php
// logout.php

session_start();            // Start the session
session_unset();            // Unset all session variables
session_destroy();          // Destroy the session

// Optional: Also clear session cookie (for good measure)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect to homepage or login
header("Location: index.php");
exit;
