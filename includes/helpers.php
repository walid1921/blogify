<?php

function activeLink($pageName) {
    $currentPage = basename($_SERVER["SCRIPT_FILENAME"]);
    return $currentPage === $pageName ? "active" : ""; 
}

function pageClass() {
    return basename($_SERVER["SCRIPT_FILENAME"], ".php");
}


function isLoggedIn() {
    return isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;
}

function redirect($location) {
    header("Location: {$location}");
    exit;
}

function isAdmin(){
    return isset($_SESSION["admin"]) && $_SESSION["admin"] === true;
}

function checkServer($conn, $result) { // to check if the server is working
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    return true;
}

// error_log("This is an error message", 3, "/path/to/logfile.log"); 
// This sends the error message to a log file instead of displaying it on the screen. 
// 3 indicates appending to the log file, 
// /path/to/logfile.log is the path where you want to store the log.
function handleError($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $errorMessage = date('[Y-m-d H:i:s]') . " Error: [$errno] $errstr in $errfile on line $errline" . PHP_EOL;
    error_log($errorMessage, 3, __DIR__ . '/../error_log.txt');

    if (ini_get('display_errors')) {
        echo "<div class='error'>An error occurred. Please try again later.</div>";
    }

    return true;
}

set_error_handler('handleError');
