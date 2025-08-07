<?php
require_once __DIR__ . '/../env.php';
// the difference between include and require is that require will throw an error if the file is not found, while include will only emit a warning and continue execution.
// include good to use when the file is not critical to the application (example calling a component inside an html), while require is used for files that are essential for the application to run properly.

class Database {
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $pdo;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->dbname = $_ENV['DB_NAME'];

        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // for testing: echo "Connected successfully!";
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}


//! Explanation:

// We use Herd as our PHP development environment, and a MySQL server installed and running,
// then we need to create a database.
// In PHP, we have various APIs to connect our project to the database:

// PDO (PHP Data Objects): it is more flexible and supports multiple databases (MySQL, SQLite, Postgres). It also supports prepared queries and is considered more modern and secure against SQL injection. ($dsn = "mysql: => this is where we specify the type of database we are connecting to, in this case, MySQL).
// MySQLi: Good for learning. Only supports MySQL databases. Its use is not recommended because it is not secure enough.
// ODBC (Open Database Connectivity): Allows connections to many different types of databases, including remote databases. Often used in large companies. for example, if you want to connect to a Microsoft SQL Server database, you would use ODBC.


// So in this code we have a Database class that connects to a MySQL database using PDO (PHP Data Objects).
// The purpose of this class is to centralize and manage the database connection, making your code cleaner, more secure, and easier to maintain.

// The class has private properties for the database connection parameters: host, user, password, dbname, and pdo.
// The hostname or IP address where the database is running. In this case, locally. But in production, it would be the IP address of the remote server running the database.
// Username and password for accessing the database
// The name of the database our project should connect to.

// The constructor initializes these properties using environment variables, which are stored in .env and loaded with env.php for security reasons, so they are not hard-coded and exposed in the codebase. This is a good practice to keep sensitive information out of the source code.

// The constructor is a special method that runs automatically when an instance of the class is created.
// The Data Source Name (DSN) string tells PDO how to connect to the database.

// try...catch block is used to handle any exceptions that might occur during the connection process.
// If the connection is successful, the PDO instance (built-in function) is stored in the $pdo property of the class, which can be accessed; This is where the actual connection to the database server happens.
// Now the $pdo variable holds the connection object. And it sets the error mode to exception using setAttribute(). The PDO::ATTR_ERRMODE and PDO::ERRMODE_EXCEPTION constants are used to set the error reporting mode.
// If the connection fails, it prints a user-friendly error message and terminates the script with die().
// (to test the error change one of the variables "rot" instead of "root")

// The getConnection method is a simple public function that returns the PDO instance that was created in the constructor for use in other parts of the application.




// To use the class, you would create a new Database object and then call getConnection() to get a handle to the database.

// Create a new database object
//$database = new Database();

// Get the PDO connection from the object
//$conn = $database->getConnection();

// $conn is now a PDO object you can use to run queries.
// function getUserByUsername($conn, $username) {
//    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
//    $stmt->execute(['username' => $username]);
//    $result = $stmt->fetch(PDO::FETCH_ASSOC);
//    return $result; // returns one user or false
//}
//-	we use prepare() method to protect the database and sanitize inputs from SQL injection (ex: someone can inject SQL code to delete database).
// So, prepare() used to prepares an SQL query for execution using placeholders. It takes a SQL query as an argument and returns a statement object.
//-	the SQL query checks if a row exists in the users table where the username, email matches a given value (placeholders).  Means SQL query is prepared with placeholders (:)
//-	The execute() method runs the prepared SQL query with the values bound earlier. At this point, the database checks if there are any rows in the users table where: The username matches $username, OR The email matches $email.
// PDO::FETCH_ASSOC: Return the result as an associative array (e.g., ['id' => 1, 'username' => 'john']). (column name => value).
//-	The fetch method to fetch the result from the query. $r
//result now holds the database response, which could be a row or Empty. If username or email exists, show error – If no error, insert new user into database.
