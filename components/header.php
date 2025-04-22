<?php
// PHP hide errors by default, so you need to enable them (Note: In production environments, it's recommended to log errors rather than display them to users for security reasons.)
// Check helpers.php

error_reporting(E_ALL); // Reports all errors & warnings.
ini_set('display_errors', '0'); //  Ensures errors are displayed in the browser by changing to 1
ini_set('display_startup_errors', '0');

// Use var_dump() and print_r() (Check Variables)
// $test = ["name" => "John", "age" => 30];
// var_dump($test); // Detailed variable info
// print_r($test);  // Readable format for arrays/objects

// If you’re debugging database queries : echo $conn->error;  // Show SQL errors
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body>

<?php include "./components/navigation.php"; ?>
