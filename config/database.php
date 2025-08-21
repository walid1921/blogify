<?php
require_once __DIR__ . '/../env.php';


class Database {
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $pdo;
    private static $instance;

    public function __construct($autoSetup = false) {
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->dbname = $_ENV['DB_NAME'];

        if ($autoSetup) {
            $this->createDatabaseIfNotExists();
            $this->importStructure(__DIR__ . 'config/db-structure.sql');
        }

        $this->connect();
    }

    // Get a singleton instance of Database, it will be used in migration.php and other files
    // This method ensures that only one instance of the Database class is created, which is a common design pattern known as the Singleton pattern.
    // This is useful to avoid multiple connections to the database, which can lead to performance issues and resource exhaustion.
    public static function getInstance($autoSetup = false) {
        if (self::$instance === null) {
            self::$instance = new self($autoSetup);
        }
        return self::$instance;
    }



    // Establish database connection
    private function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//            echo "✅ Connected to database '{$this->dbname}'<br>";
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Create database if it does not exist
    private function createDatabaseIfNotExists() {
        try {
            $pdo = new PDO("mysql:host={$this->host};charset=utf8mb4", $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = :dbname");
            $stmt->execute([":dbname"=> $this->dbname]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                echo "⚠️ Database '{$this->dbname}' already exists<br>";
                return;
            } else {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                echo "✅ Database '{$this->dbname}' created successfully<br>";
            }


        } catch (PDOException $e) {
            die("Database creation failed: " . $e->getMessage());
        }
    }


    // Import database structure from SQL file
    // This method reads an SQL file and executes its contents to set up the database structure.
    // It is useful for initializing the database with tables, indexes, and other schema elements.
    private function importStructure($sqlFilePath) {
        if (!file_exists($sqlFilePath)) {
            die("⚠️ SQL file not found: {$sqlFilePath}");
        }

        try {
            $pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4", $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = file_get_contents($sqlFilePath);
            $pdo->exec($sql);

            echo "✅ Database structure imported successfully from '{$sqlFilePath}'<br>";
        } catch (PDOException $e) {
            die("Import failed: " . $e->getMessage());
        }
    }

    // Get the PDO connection instance
    public function getConnection() {
        return $this->pdo;
    }


    // Execute SELECT queries and return all results
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }


    // Execute SELECT queries and return single result
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }


    // Execute INSERT, UPDATE, DELETE queries
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            return [
                'success' => $result,
                'lastInsertId' => $this->pdo->lastInsertId(),
                'rowCount' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            throw new Exception("Execute failed: " . $e->getMessage());
        }
    }


    // Check if table exists
    public function tableExists($tableName) {
        $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?";
        $result = $this->queryOne($sql, [$this->dbname, $tableName]);
        return $result['COUNT(*)'] > 0;
    }


    // Get database name
    public function getDatabaseName() {
        return $this->dbname;
    }


    // Transaction methods
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollBack();
    }


    // Execute raw SQL (use with caution)
    public function exec($sql) {
        try {
            return $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new Exception("Exec failed: " . $e->getMessage());
        }
    }


    // Get last insert ID
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    // Prevent cloning and unserializing
    private function __clone() {}

    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}


//! Explanation:
// After we set up the Herd as our PHP development environment, and a MySQL server installed and running, we need to create a database and connect to it.
// In PHP, we have various APIs to connect our project to the database:

// PDO (PHP Data Objects): it is more flexible and supports multiple databases (MySQL, SQLite, Postgres). It also supports prepared queries and is considered more modern and secure against SQL injection. ($dsn = "mysql: => this is where we specify the type of database we are connecting to, in this case, MySQL).
// MySQLi: Good for learning. Only supports MySQL databases. Its use is not recommended because it is not secure enough.
// ODBC (Open Database Connectivity): Allows connections to many different types of databases, including remote databases. Often used in large companies. for example, if you want to connect to a Microsoft SQL Server database, you would use ODBC.


// So in this code we have a Database class that connects to a MySQL database using PDO (PHP Data Objects).
// The purpose of this class is to centralize and manage the database connection, making your code cleaner, more secure, and easier to maintain.

// The class has private properties for the database connection parameters (environment variables): host, user, password, dbname, and pdo.
// The hostname or IP address where the database is running. In this case, locally. But in production, it would be the IP address of the remote server running the database.
// Username and password for accessing the database
// The name of the database our project should connect to.

// The constructor initializes these properties using environment variables, which are stored in .env and loaded with env.php for security reasons, so they are not hard-coded and exposed in the codebase. This is a good practice to keep sensitive information out of the source code and also to keep variables dynamic, so that when the project is in production, it will use the environment variables of the server instead of the local one.

// The constructor is a special method that runs automatically when an instance of this class is created.
// The DSN (Data Source Name) string tells PDO how to connect to the database.
// charset=utf8mb4 for character encoding (like emojis and special characters ç, ñ, $)

// try...catch block is used to handle any exceptions that might occur during the connection process.
// If the connection is successful, the PDO instance (built-in function) is stored in the $pdo property of the class, which can be accessed; This is where the actual connection to the database server happens.
// Now the $pdo variable holds the connection object. And it sets the error mode to exception using setAttribute(). The PDO::ATTR_ERRMODE and PDO::ERRMODE_EXCEPTION constants are used to set the error reporting mode, useful for debugging to trace where the error is coming from
// If the connection fails, it prints a user-friendly error message and terminates the script with die().
// (to test the error change one of the variables "rot" instead of "root")


// When a developer runs the script through your web server or command line (php config/setup.php) for the first time.
// The database and tables will be created automatically
// $database = new Database(autoSetup: true); // true to auto-setup the database, if we already have a database, we can leave it empty or false.
// $conn = $database->getConnection();

// The constructor checks if the database exists and creates it if it doesn't.
// The createDatabaseIfNotExists() method connects to the MySQL server and creates the database if it doesn't already exist.
// It uses the PDO class to connect to the MySQL server and execute a SQL command to create the database.
// The importStructure() reads and executes your db_structure.sql file to create all tables and insert default data.
// This method is useful for initializing the database with tables, indexes, and other schema elements.
// Once the database is created or already exists, the connect() method is called to establish the connection to the database using PDO.



// The getConnection method is a simple public function that returns the PDO instance that was created in the constructor for use in other parts of the application.




//! The Use:
// we create an instance of the Database class and then call getConnection() to get a handle to the database.

// $database = new Database(); // here its false by default as // we don't want to auto-setup the database every time we run the code, only once.
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


// SQL injection is a code that can be passed to inputs form that can corrupt or expose the database.
// We use prepare() method to protect the database and sanitize inputs from SQL injection (ex: someone can inject SQL code to delete database).
// So, prepare() will prepare the SQL query for execution using placeholders. It takes a SQL query as an argument and returns a statement object.
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


//! Test

//* Q1: What’s the main difference between include and require in PHP, and why is require used in env.php?
// the difference between include and require is that:
// require:  is used for files that are essential to keep the app running => if the file is not found, it throws an error and stop execution  => (example .env is important for database connection)
// include: Emits a warning if the file is missing but continues executing. is used when the file is not essential exp: component of an HTML)
// require is used for env.php because environment configuration is essential. If it's missing, the application cannot run safely.

//* Q2: In the constructor, environment variables are loaded from .env instead of hardcoding credentials. Why is this considered good practice?
// - Keeps sensitive data (DB passwords, API keys) out of source code.
// - Allows changing credentials without editing code.
// - Makes deployment easier across multiple environments (dev, staging, prod).

//* Q3: What does "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4" represent?
//  - This is the DSN (Data Source Name) string for PDO, it tells the PDO which type of database to connect  in that case mysql,
//  - Specifies:
//    host = database server address
//   dbname = database name
//   charset = utf8mb4 for full Unicode support (including emojis)

//* Q4: Why is PDO::ATTR_ERRMODE set to PDO::ERRMODE_EXCEPTION after connecting?
// - Configures PDO to throw exceptions on errors instead of silent failures or warnings.
// - Makes debugging easier and helps catch issues early in development.

//* Q5: Why might using a singleton pattern for database connections be beneficial in a web application?
//  - Ensures only one DB connection per request.
//  - Performance: Reduces overhead from repeatedly creating new connections.
//  - Centralizes connection handling for easier maintenance.

//* Q6: In importStructure(), why do we call file_get_contents() before $pdo->exec()?
//  - file_get_contents() reads the entire SQL file into a string.
//  - $pdo->exec() executes that string as a batch of SQL commands.

//* Q7: What is SQL injection, and how does using prepare() and execute() prevent it?
//  - SQL Injection: An attack where malicious SQL is inserted into a query through from's inputs that may corrupt or expose the database.
//  - we can use prepare( ) method to protect the database and sanitize the inputs form.
//  - prepare(): will prepare the query for execution using placeholders. it takes the query as an argument and returns a stmt object. Sends the query structure to the DB without data values.
//  - execute(): Sends data separately, preventing it from being treated as SQL code. and it binds the data to the placeholders, ensuring that user input is treated as data, not executable code.


//* Q8: When should you use queryOne() vs query() in this class?
// - queryOne(): When expecting exactly one row/result using fetch() => it returns a single result or false.
// - query(): When expecting multiple rows/results using fetchAll() => it returns an array of all results.


//* Debug & Predict
//* Q1: What will be printed if this runs and the DB credentials are wrong?
// $db = new Database();

//* Q2: What happens if the SQL file in importStructure() is missing?
// - The script will terminate with an error message: "SQL file not found: {$sqlFilePath}".
// - The database structure will not be imported, and the application may not function correctly.

//* Q3: What will be printed? (Assume database does not exist, but db-structure.sql exists.)
// $db = new Database(autoSetup: true);

//* Q4: Spot the bug in this line:
//$this->importStructure(__DIR__ . 'config/db-structure.sql');
// - The path is incorrect; it should be __DIR__ . '/../config/db-structure.sql' to correctly point to the SQL file.

//* Q5: What happens if you call getConnection() before the constructor has run?
// - It will throw an error because the $pdo property is not initialized yet.
// - The Database instance must be created first to establish the connection.


//* Q6: what is wrong with this vulnerable code, and how can it be fixed?
//function getUserById($conn, $id) {
//    $sql = "SELECT * FROM users WHERE id = $id";
//    return $conn->query($sql)->fetchAll();
//}
// - The code is vulnerable to SQL injection because it directly interpolates the $id variable into the SQL query.

// - To fix it, use prepared statements with placeholders:
// function getUserById($conn, $id) {
//     $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id
//     $stmt->execute([':id' => $id]);
//     return $stmt->fetchAll();
// }
// - This way, the $id value is bound as a literal value, preventing any SQL injection attempts.


//* Mini Coding Tasks (Write short code snippets for each)
// Write a function using the Database class that retrieves all active users (is_active = 1) and returns them as an associative array.
// Using this class, write code to insert a new row into the users table with username and password fields.
// Write code to update the is_active field of a given user_id to false.
// Use tableExists() to check if a table named "orders" exists, and if not, create it.
// Write a safe function deleteUserById() that removes a user from the users table using prepared statements.
