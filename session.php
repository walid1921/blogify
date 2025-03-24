<?php 

//! Secure session start
session_start([
    'use_only_cookies' => 1,        // Prevents session ID from being passed in the URL, and make sure that its used only as a cookie.
    'use_strict_mode' => 1,         // Prevents attackers from setting a predefined session ID.
    'cookie_httponly' => 1,         // Stops JavaScript from accessing session cookies (prevents XSS attacks).
    'cookie_secure' => 0,           // Ensures the session cookie is sent only over HTTPS. Set it to 0 if your website is on HTTP
    'cookie_samesite' => 'Strict',  // Protects against Cross-Site Request Forgery (CSRF) attacks.
]);

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


