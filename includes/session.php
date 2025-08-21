<?php
session_start([
    'use_only_cookies' => 1, // this ensures that PHP only uses cookies to store session data, to prevent session fixation attacks
    'use_strict_mode' => 1, // this ensures that PHP will not accept uninitialized session IDs, to prevent session fixation attacks
    'cookie_httponly' => 1, // this ensures that the session cookie is only accessible via HTTP, to prevent JavaScript access
    'cookie_secure' => 1, // this ensures that the session cookie is only sent over HTTPS, to prevent eavesdropping
    'cookie_samesite' => 'Strict', // this ensures that the session cookie is only sent to the same site, to prevent CSRF attacks
]);

require_once "helpers.php";

//! Regenerate session ID every 5min and after every new login.
if (!isset($_SESSION['LAST_REGEN']) || (time() - $_SESSION['LAST_REGEN']) > 300) { // 300s = 5min
    session_regenerate_id(true); // regenerates a new session ID & deletes the old one
    $_SESSION['LAST_REGEN'] = time();
}

//! Destroy session after inactivity
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) { // 1800s = 30min
    session_unset();
    session_destroy();
    redirect("login.php");
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity
