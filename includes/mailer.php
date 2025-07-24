<?php
function send_email($to, $subject, $message, $headers = '') {
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Animal IQ <no-reply@animal-iq.local>\r\n";

    return mail($to, $subject, $message, $headers);
}
?>
