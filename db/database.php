<?php

// Here is a list of the diffrent APIs in PHP to connect to a database:
// MySQLi (improved): supports both procedural and object-oriented programming styles - recommended for beginners 
// PDO (PHP Data Objects) : supports multiple databases and is more secure than MySQLi - recommended for advanced users 
// ODBC (Open Database Connectivity) : supports multiple databases and is used for connecting to remote databases - recommended for advanced users 

// 1. MySQLi 

$host = "localhost"; // hostName or IP address
$user = "root";
$password = "root";
$dbname = "registration_test";

$conn = new mysqli($host, $user, $password, $dbname);

 
if ($conn->connect_error) {  // Check connection
    die("Connection failed :" . $conn->connect_error);  // die() function prints a message and exits the current script
} 
// else if ($conn) {
//     echo "Connection successful";
// }

?>