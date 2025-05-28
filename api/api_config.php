<?php
define('API_ALLOWED_IPS', [
    '127.0.0.1',
    '113.23.51.97'
]);
define('API_SECRET_KEY', 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855');

function checkIP()
{
    $clientIP = $_SERVER['REMOTE_ADDR'];
    if ($clientIP === "::1") {
        return true;
    }
    return in_array($clientIP, API_ALLOWED_IPS);
}