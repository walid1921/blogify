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
//            echo "✅ Connected to database '{$this->dbname}'.\n";
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Create database if it does not exist
    private function createDatabaseIfNotExists() {
        try {
            $pdo = new PDO("mysql:host={$this->host};charset=utf8mb4", $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "✅ Database '{$this->dbname}' created or already exists.\n";
        } catch (PDOException $e) {
            die("Database creation failed: " . $e->getMessage());
        }
    }


    // Import database structure from SQL file
    // This method reads an SQL file and executes its contents to set up the database structure.
    // It is useful for initializing the database with tables, indexes, and other schema elements.
    private function importStructure($sqlFilePath) {
        if (!file_exists($sqlFilePath)) {
            die("SQL file not found: {$sqlFilePath}");
        }

        try {
            $pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4", $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = file_get_contents($sqlFilePath);
            $pdo->exec($sql);

            echo "✅ Database structure imported successfully from '{$sqlFilePath}'.\n";
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

// The class has private properties for the database connection parameters: host, user, password, dbname, and pdo.
// The hostname or IP address where the database is running. In this case, locally. But in production, it would be the IP address of the remote server running the database.
// Username and password for accessing the database
// The name of the database our project should connect to.

// The constructor initializes these properties using environment variables, which are stored in .env and loaded with env.php for security reasons, so they are not hard-coded and exposed in the codebase. This is a good practice to keep sensitive information out of the source code.

// The constructor is a special method that runs automatically when an instance of this class is created.
// The Data Source Name (DSN) string tells PDO how to connect to the database.
// utf8mb4 for character encoding, which supports a wide range of characters, including emojis.

// try...catch block is used to handle any exceptions that might occur during the connection process.
// If the connection is successful, the PDO instance (built-in function) is stored in the $pdo property of the class, which can be accessed; This is where the actual connection to the database server happens.
// Now the $pdo variable holds the connection object. And it sets the error mode to exception using setAttribute(). The PDO::ATTR_ERRMODE and PDO::ERRMODE_EXCEPTION constants are used to set the error reporting mode.
// If the connection fails, it prints a user-friendly error message and terminates the script with die().
// (to test the error change one of the variables "rot" instead of "root")


// When a developer run the script through your web server or command line (php config/setup.php) for the first time.
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



//! Database Migration
// Migration is the process of updating the database schema or data structure without losing existing data.
// It allows developers to version control their database changes, just like Git tracks the code changes, making it easier to manage updates and rollbacks.
// The migration system is particularly useful for production applications where you need to track database changes over time and deploy them systematically across different environments.Why Do We Need Migrations?
//The Problem Without Migrations:
//Imagine you're working on your project:
//
//Day 1: You create users table manually in DataGrip
//Day 5: You add a phone column to users table
//Day 10: You create blogs table
//Day 15: You add is_published column to blogs
//Day 20: Your teammate joins the project
//
//* Problem: How does your teammate get the same database structure? They would need to:
//
//Manually create all tables
//Remember to add all the columns you added
//Make sure foreign keys are correct
//Insert the same sample data
//
//* Bigger Problem: When you deploy to your live server:
//
//Which changes have you already applied?
//Which ones are new?
//What if you make a mistake and break the live database?
//
//* The Solution With Migrations:
//Instead of manual changes, you write scripts that describe each change:
// Migration 001: Create users table
//"CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50)...)"
// Migration 002: Add phone column
//"ALTER TABLE users ADD COLUMN phone VARCHAR(15)"
// Migration 003: Create blogs table
//"CREATE TABLE blogs (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255)...)"


//* How Migrations Work
//Migration System Components:
//
//Migration.php = The class that handles running migrations
//migrate.php = The command-line script you run
//migrations table = Keeps track of which migrations have been run

//1. You write a migration (database change)
//2. Run: php migrate.php migrate
//3. System checks: "Has this migration been run before?"
//4. If NO: Run the migration + record it in migrations table
//5. If YES: Skip it (never run twice)


//* Real-World Example
//Let's say you're working with a team:
//Week 1 - You create initial database:
//php migrate.php migrate
//
//Runs:
//
//Migration 001: Create users table
//Migration 002: Create categories table
//Migration 003: Create blogs table
//
//Week 2 - You add a feature:
//You need to add is_featured column to blogs. Instead of manually adding it, you:
//
//1- Add new migration to Migration.php:
//'004_add_featured_to_blogs' => "ALTER TABLE blogs ADD COLUMN is_featured BOOLEAN DEFAULT FALSE"
//
//2- Run migration: php migrate.php migrate
//
//3- Deploy to server: ./deploy.sh db  # Only runs new migrations on server
//
//
//Week 3 - Your teammate pulls your code:
//They run: php migrate.php migrate\
//
//The system:
//✅ Skips migrations 001-003 (already exist)
//✅ Runs migration 004 (new one)
//✅ Their database now matches yours exactly
//
//Migration Commands Explained
//Migration.php - The Engine
//
//Contains the migration logic
//Tracks which migrations have been run
//Handles database transactions
//Creates the migrations table
//
//migrate.php - The Interface
//Commands you can run:
# Run all pending migrations
//php migrate.php migrate

# See what's in your database
//php migrate.php status

# Add sample data
//php migrate.php seed

# Start completely fresh (DELETE EVERYTHING!)
//php migrate.php fresh

# Create backup
//php migrate.php backup


//* Benefits of This System
//1. Team Collaboration
//Everyone gets the same database structure
//No more "it works on my machine" database issues

//2. Safe Deployments
//Never accidentally run the same change twice
//Automatic backups before major changes
//Can deploy database changes without manual SQL

//3. History Tracking
//See exactly what changed and when
//Can add rollback functionality later

//4. Environment Consistency
//Development database = Staging database = Production database
//Same structure everywhere

//* Example Migration Flow
# Day 1: Fresh start
//php migrate.php fresh
# Creates: users, categories, blogs, tasks tables + sample data

# Day 5: You add a feature requiring new column
# (You edit Migration.php to add new migration)
//php migrate.php migrate
# Adds: is_featured column to blogs

# Day 5: Deploy to server
//    ./deploy.sh db
# Server now has the new column too

# Day 10: Teammate pulls your code
//git pull
//php migrate.php migrate
# Their database gets the new column automatically


//* Summary
//Migrations = Version control for your database
//Migration.php = The system that runs and tracks database changes
//migrate.php = The command you use to apply those changes

//Why use it? = Team collaboration, safe deployments, consistent environments
//Instead of manually clicking in DataGrip and hoping everyone does the same thing, you write code that automatically keeps everyone's database in sync!
