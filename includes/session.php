<?php
session_start([
    'use_only_cookies' => 1, // this ensures that PHP only uses cookies to store session data, to prevent session fixation attacks
    'use_strict_mode' => 1, // this ensures that PHP will not accept uninitialized session IDs, to prevent session fixation attacks
    'cookie_httponly' => 1, // this ensures that the session cookie is only accessible via HTTP, to prevent JavaScript access
    'cookie_secure' => 1, // this ensures that the session cookie is only sent over HTTPS, to prevent eavesdropping
    'cookie_samesite' => 'Strict', // this ensures that the session cookie is only sent to the same site, to prevent CSRF attacks
]);

require_once "helpers.php";

//! Regenerate session ID every 5 minutes and After a user logs in.
// This prevents attackers from using stolen session IDs.
if (!isset($_SESSION['LAST_REGEN']) || time() - $_SESSION['LAST_REGEN'] > 300) {
    session_regenerate_id(true); // Generates a new session ID & deletes the old one
    $_SESSION['LAST_REGEN'] = time();
}

//! Check for session hijacking
// Store the user’s IP and browser details. Why? If an attacker steals a session but has a different IP/browser, they will be logged out
if (!isset($_SESSION['USER_IP'])) {
    $_SESSION['USER_IP'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

} else if  ($_SESSION['USER_IP'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_destroy(); // End session
    redirect("login.php");
}

//! Destroy session after inactivity
// This ensures attackers cannot reuse old sessions.
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) { // 600 seconds = 10 minutes
    session_unset();
    session_destroy();
    redirect("login.php");
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity



// Store Sessions Securely in the database makes it harder for attackers to steal session data. (optional, but recommended)
// By default, PHP stores sessions in files, but storing them in a database is more secure.
// 👉 Change session storage to a database: 1️⃣ Create a table in MySQL:
// CREATE TABLE sessions (
//     id VARCHAR(128) PRIMARY KEY,
//     data TEXT NOT NULL,
//     last_access INT NOT NULL
// );
// class SecureSessionHandler extends SessionHandler {
//     public function read($id) {
//         return parent::read($id) ?: ''; // Prevent empty session issues
//     }
// }

// $handler = new SecureSessionHandler();
// session_set_save_handler($handler, true);