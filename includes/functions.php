<?php
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function formatDateTime($datetime) {
    return date("F j, Y, g:i a", strtotime($datetime));
}

function is_logged_in() {
    return isset($_SESSION['user_email']);
}
?>
