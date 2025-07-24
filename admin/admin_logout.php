<!-- admin/admin_logout.php -->
<?php
session_start();
session_unset();
session_destroy();
header("Location: admin_login.php");
exit;
?>
