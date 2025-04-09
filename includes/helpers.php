<?php 


function activeLink($pageName) {
    $currentPage = basename($_SERVER["SCRIPT_FILENAME"]);
    return $currentPage === $pageName ? "active" : ""; 
}

function pageClass() {
    return basename($_SERVER["SCRIPT_FILENAME"], ".php");
}

// isLoggedIn() checks if the user is logged in by checking the session variable.
function isLoggedIn() {
    return isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;
    // so basically here its double check if the logged_in variable exists and if it's true - this variable will be set in the login functionality (when user login-in)
}

// redirect() is a function that takes a URL as an argument and redirects the user to that URL using the header().
// Then it exits the script. to ensure no further code is executed after the redirect.
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

function editUser($pdo, $userId, $newUsername, $newEmail) {
    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
    return $stmt->execute([':username' => $newUsername, ':email' => $newEmail, ':id' => $userId]);
}

function deleteUser($pdo, $userId) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    return $stmt->execute([':id' => $userId]);
}