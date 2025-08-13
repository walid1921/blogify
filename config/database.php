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
// After we set up the Herd as our PHP development environment, and a MySQL server installed and running, we need to create a database.
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

// The constructor is a special method that runs automatically when an instance of this class is created.
// The Data Source Name (DSN) string tells PDO how to connect to the database.

// try...catch block is used to handle any exceptions that might occur during the connection process.
// If the connection is successful, the PDO instance (built-in function) is stored in the $pdo property of the class, which can be accessed; This is where the actual connection to the database server happens.
// Now the $pdo variable holds the connection object. And it sets the error mode to exception using setAttribute(). The PDO::ATTR_ERRMODE and PDO::ERRMODE_EXCEPTION constants are used to set the error reporting mode.
// If the connection fails, it prints a user-friendly error message and terminates the script with die().
// (to test the error change one of the variables "rot" instead of "root")

// The getConnection method is a simple public function that returns the PDO instance that was created in the constructor for use in other parts of the application.




//! The Use:
// we create an instance of the Database class and then call getConnection() to get a handle to the database.

// $database = new Database();
// $conn = $database->getConnection();

// $conn is now a PDO object you can use to run queries.
// function getUserByUsername($conn, $username) {
//    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
//    $stmt->execute(['username' => $username]);
//    $result = $stmt->fetch(PDO::FETCH_ASSOC);
//    var_dump($result); // for testing
//    return $result ? $result['username'] : null; // returns one user or false
//}

//$username = getUserById($connection, 87); // this will be used somewhere else in the code, like in a controller or model.
//
//if ($username) {
//    echo "Username for user ID 1: $username";
//} else {
//    echo "User not found.";
//}


// In HTML

// <h1>Welcome to the User Management System</h1>
// <h2>The user has been called is:</h2>
// <?php if ($username) :
// <p>--><?php //echo htmlspecialchars($username); <!--</p>-->
// <?php //endif



// We use prepare() method to protect the database and sanitize inputs from SQL injection (ex: someone can inject SQL code to delete database).
// So, prepare() used to prepares an SQL query for execution using placeholders. It takes a SQL query as an argument and returns a statement object.
// The SQL query checks if a row exists in the user table where the username matches a given value (placeholders).  Means SQL query is prepared with placeholders (:)
// The execute() method runs the prepared SQL query with the values bound earlier. At this point, the database checks if there are any rows in the users table where: The username matches $username.
// fetch(PDO::FETCH_ASSOC); Return the result as an associative array (e.g., ['id' => 1, 'username' => 'john']). (column name => value).
// The fetch method to fetch the result from the query.
// or fetchAll() to fetch all results as an array of associative arrays.
// $result now holds the database response, which could be a row or Empty. If a username doesn't exist, show an error – If no error, return that $username value.



//! EXAMPLE OF SQL INJECTION VULNERABILITY AND PREVENTION

//<?php
//
//require_once "database.php";
//
//$database = new Database();
//$conn = $database->getConnection();
//
// ❌ Vulnerable version === variable directly inside the SQL string
//
//function getUserByID_vulnerable($conn, $userId) {
//    $sql = "SELECT * FROM users WHERE id = $userId";
//    $stmt = $conn->query($sql); // executes raw SQL
//    return $stmt->fetchAll(PDO::FETCH_ASSOC);
//}
//
// Injection attempt
//$input = "87 OR '1'='1'";  => '1'='1' means true
//$users_vulnerable = getUserByID_vulnerable($conn, $input);
//
//var_dump($users_vulnerable);
//
//
//
// ✅ Safe version === parameterized prepared statement
//
//function getUserByID_safe($conn, $userId) {
//    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
//    $stmt->execute([':id' => $userId]); // bound as literal value
//    return $stmt->fetchAll(PDO::FETCH_ASSOC);
//}
//
// Same injection attempt
//$users_safe = getUserByID_safe($conn, $input);
//
//var_dump($users_safe);
//
