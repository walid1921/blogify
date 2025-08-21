<?php

//echo __DIR__;

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

function day_month_year($date) {
    $timestamp = strtotime($date);
    return date('d M Y', $timestamp);
}

function checkServer($conn, $result) { // to check if the server is working
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    return true;



}


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


//function base_url($path = "") {
//    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
//    $host = $_SERVER['HTTP_HOST'];
//    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
//    $baseUrl = $protocol . $host . $scriptName;
//
//    return rtrim($path, '/');
//}
