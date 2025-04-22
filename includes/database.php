<?php

$host = "localhost";
$user = "root";
$password = "root";
$dbname = "registration_test";



$dsn = "mysql:host=$host;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    echo "Connected successfully!";
} catch (PDOException $e) {
    die("Connection failed :" . $e->getMessage());
}


// Then go to register.php file and follow
