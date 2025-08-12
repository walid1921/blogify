<?php

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';

// Destroying a Session
session_unset(); // to remove all session variables or you can use $_SESSION = array(); to clear the session array
// Note: session_unset() does not delete the session cookie in the browser, it just clears the session data on the server.
// If you want to delete the session cookie in the browser,
// you can set the cookie with an expiration time in the past.
//if (ini_get("session.use_cookies")) {
//    $params = session_get_cookie_params();
//    setcookie(session_name(), '', time() - 42000,
//        $params["path"], $params["domain"],
//        $params["secure"], $params["httponly"]
//    );
//}


session_destroy(); // to destroy the session, it deletes the session ID on the server and not the session ID cookie in the browser. buy you don't see yhe effect until you refresh the page

redirect("login.php");
