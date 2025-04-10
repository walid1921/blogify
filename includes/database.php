<?php

$host = "localhost"; // Hostname or the IP address of the server where the database is running. It is set to localhost for a local database.
$user = "root"; // The username used to connect to the database.
$password = "root";
$dbname = "registration_test";

// 1. MySQLi
//$conn = new mysqli($host, $user, $password, $dbname);

//if ($conn->connect_error) {
//    die("Connection failed :" . $conn->connect_error);
//}
// else if ($conn) {
//     echo "Connection successful";
// }



// 2. PDO : php database object
$dsn = "mysql:host=$host;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password); // This creates a new PDO object, which serves as the primary connection to the database.
    $pdo-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // This sets the error mode to exception, which means that any database errors will throw exceptions.
//    echo "Connected successfully!";
} catch (PDOException $e) { // This catches any exceptions thrown during the connection process. e : the exception object that contains information about the error.
    die("Connection failed :" . $e->getMessage()); // This terminates the script and displays the error message.
}

// To change the database name and password through terminal
// 1. Open the terminal and navigate to the directory where your database is located.
// 2. Use the command line to access your database management system (e.g., MySQL, PostgreSQL).
// 3. Use the command to connect to your database (e.g., mysql -u root -p).
// 4. Enter your password when prompted.
// 5. Use the command to change the database name (e.g., ALTER DATABASE old_db_name RENAME TO new_db_name;).
// 6. Use the command to change the password (e.g., ALTER USER 'username'@'localhost' IDENTIFIED BY 'new_password';).
// 7. Exit the database management system (e.g., exit;).



// Then go to register.php file and follow