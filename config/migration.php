<?php

require_once __DIR__ . '/Database.php';

class Migration {
    private $db;
    private $migrationsTable = 'migrations';

    public function __construct() {
        $this->db = Database::getInstance();
        $this->createMigrationsTable();
    }

    /**
     * Create migrations table if it doesn't exist
     */
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        try {
            $this->db->exec($sql);
        } catch (Exception $e) {
            throw new Exception("Failed to create migrations table: " . $e->getMessage());
        }
    }

    /**
     * Run a single migration
     */
    public function runMigration($migrationName, $sql) {
        try {
            // Check if migration already executed
            $existing = $this->db->queryOne(
                "SELECT COUNT(*) as count FROM {$this->migrationsTable} WHERE migration = ?",
                [$migrationName]
            );

            if ($existing['count'] > 0) {
                echo "⏭️  Migration '$migrationName' already executed\n";
                return true;
            }

            // Start transaction
            $this->db->beginTransaction();

            // Execute the migration SQL
            $this->db->exec($sql);

            // Record the migration
            $this->db->execute(
                "INSERT INTO {$this->migrationsTable} (migration) VALUES (?)",
                [$migrationName]
            );

            // Commit transaction
            $this->db->commit();

            echo "✅ Migration '$migrationName' executed successfully\n";
            return true;

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            echo "❌ Migration '$migrationName' failed: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Run all migrations
     */
    public function runAllMigrations() {
        echo "\n🚀 Starting database migrations...\n";
        echo "=====================================\n";

        $migrations = $this->getMigrations();
        $success = 0;
        $failed = 0;

        foreach ($migrations as $name => $sql) {
            if ($this->runMigration($name, $sql)) {
                $success++;
            } else {
                $failed++;
                // Stop on first failure to prevent data corruption
                break;
            }
        }

        echo "\n📊 Migration Summary:\n";
        echo "✅ Successful: $success\n";
        echo "❌ Failed: $failed\n";
        echo "=====================================\n";

        return $failed === 0;
    }

    /**
     * Define all migrations in chronological order
     */
    private function getMigrations() {
        return [
            '001_create_users_table' => "
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    admin BOOLEAN NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )",

            '002_create_categories_table' => "
                CREATE TABLE IF NOT EXISTS categories (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL UNIQUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )",

            '003_create_blogs_table' => "
                CREATE TABLE IF NOT EXISTS blogs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    content TEXT NOT NULL,
                    author_id INT NOT NULL,
                    is_published BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
                )",

            '004_create_blog_categories_table' => "
                CREATE TABLE IF NOT EXISTS blog_categories (
                    blog_id INT NOT NULL,
                    category_id INT NOT NULL,
                    PRIMARY KEY (blog_id, category_id),
                    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
                    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
                )",

            '005_create_tasks_table' => "
                CREATE TABLE IF NOT EXISTS tasks (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    status ENUM('Not started', 'In progress', 'completed') NOT NULL DEFAULT 'Not started',
                    user_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )",

            '006_add_database_indexes' => "
                CREATE INDEX IF NOT EXISTS idx_blogs_author_id ON blogs(author_id);
                CREATE INDEX IF NOT EXISTS idx_blogs_published ON blogs(is_published);
                CREATE INDEX IF NOT EXISTS idx_blogs_created_at ON blogs(created_at);
                CREATE INDEX IF NOT EXISTS idx_tasks_user_id ON tasks(user_id);
                CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status);
                CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
                CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
            ",

            '007_insert_sample_categories' => "
                INSERT IGNORE INTO categories (name) VALUES 
                    ('SQL'), 
                    ('Python'), 
                    ('JavaScript'), 
                    ('Web Development'), 
                    ('Databases'), 
                    ('APIs')
            ",

            '008_insert_sample_users' => "
                INSERT IGNORE INTO users (username, email, password, admin) VALUES 
                    ('admin', 'admin@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
                    ('testuser', 'user@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0)
            "
        ];
    }

    /**
     * Add a new migration (for future use)
     */
    public function addMigration($name, $sql) {
        return $this->runMigration($name, $sql);
    }

    /**
     * Get list of executed migrations
     */
    public function getExecutedMigrations() {
        try {
            return $this->db->query("SELECT * FROM {$this->migrationsTable} ORDER BY executed_at");
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Check if a migration has been executed
     */
    public function migrationExists($migrationName) {
        try {
            $result = $this->db->queryOne(
                "SELECT COUNT(*) as count FROM {$this->migrationsTable} WHERE migration = ?",
                [$migrationName]
            );
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Show database status
     */
    public function showStatus() {
        echo "\n📋 Database Status:\n";
        echo "==================\n";

        $tables = ['users', 'categories', 'blogs', 'blog_categories', 'tasks', 'migrations'];

        foreach ($tables as $table) {
            try {
                if ($this->db->tableExists($table)) {
                    $result = $this->db->queryOne("SELECT COUNT(*) as count FROM $table");
                    $count = $result['count'];
                    echo "📊 $table: $count records\n";
                } else {
                    echo "❌ $table: Table not found\n";
                }
            } catch (Exception $e) {
                echo "❌ $table: Error - " . $e->getMessage() . "\n";
            }
        }

        echo "\n🔄 Executed Migrations:\n";
        $migrations = $this->getExecutedMigrations();
        foreach ($migrations as $migration) {
            echo "✅ {$migration['migration']} ({$migration['executed_at']})\n";
        }
        echo "==================\n";
    }

    /**
     * Create database backup
     */
    public function createBackup($filename = null) {
        if (!$filename) {
            $filename = "backup_" . date('Y-m-d_H-i-s') . ".sql";
        }

        $dbName = $this->db->getDatabaseName();

        // Get database credentials from Database instance
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $user = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? '';

        $command = "mysqldump --host=$host --user=$user";
        if ($password) {
            $command .= " --password=$password";
        }
        $command .= " $dbName > $filename";

        system($command, $return_var);

        if ($return_var === 0) {
            echo "✅ Database backup created: $filename\n";
            return true;
        } else {
            echo "❌ Backup failed\n";
            return false;
        }
    }
}
?>
