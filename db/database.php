<?php

$host = "localhost";
$user = "root";
$password = "root";
$dbname = "registration_test";

$conn = new mysqli($host, $user, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed :" . $conn->connect_error);
} 
// else if ($conn) {
//     echo "Connection successful";
// }


//! to check if the server is working
function checkServer($conn, $result) {
    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return true;
}

//! Create a new user
function createUser() {
    
}

?>