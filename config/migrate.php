<?php
// Migration runner script

require_once __DIR__ . '/migration.php';

class MigrationRunner {
    private $migration;

    public function __construct() {
        try {
            $this->migration = new Migration();
        } catch (Exception $e) {
            echo "❌ Failed to initialize migration system: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Run migrations
     */
    public function migrate() {
        echo "🗄️  Database Migration System\n";
        echo "============================\n";

        try {
            $success = $this->migration->runAllMigrations();

            if ($success) {
                echo "\n🎉 All migrations completed successfully!\n";
                $this->showStatus();
                return true;
            } else {
                echo "\n💥 Some migrations failed. Please check the errors above.\n";
                return false;
            }

        } catch (Exception $e) {
            echo "❌ Migration failed: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Show database status
     */
    public function status() {
        echo "🗄️  Database Status\n";
        echo "==================\n";
        $this->migration->showStatus();
    }

    /**
     * Create database backup
     */
    public function backup($filename = null) {
        echo "💾 Creating Database Backup\n";
        echo "===========================\n";
        return $this->migration->createBackup($filename);
    }

    /**
     * Fresh install (drop all tables and recreate)
     */
    public function fresh() {
        echo "🔥 Fresh Database Install\n";
        echo "========================\n";
        echo "⚠️  WARNING: This will delete all existing data!\n";

        // In a real application, you might want to add confirmation
        echo "🗄️  Proceeding with fresh install...\n";

        try {
            $db = Database::getInstance();

            // Drop tables in reverse order to handle foreign key constraints
            $tables = ['blog_categories', 'blogs', 'tasks', 'categories', 'users', 'migrations'];

            foreach ($tables as $table) {
                try {
                    $db->exec("DROP TABLE IF EXISTS $table");
                    echo "🗑️  Dropped table: $table\n";
                } catch (Exception $e) {
                    echo "⚠️  Could not drop table $table: " . $e->getMessage() . "\n";
                }
            }

            // Now run migrations
            echo "\n🚀 Running fresh migrations...\n";
            return $this->migrate();

        } catch (Exception $e) {
            echo "❌ Fresh install failed: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Seed database with sample data
     */
    public function seed() {
        echo "🌱 Seeding Database\n";
        echo "==================\n";

        try {
            $db = Database::getInstance();

            // Check if data already exists
            $userCount = $db->queryOne("SELECT COUNT(*) as count FROM users");
            if ($userCount['count'] > 2) {
                echo "📋 Sample data already exists, skipping...\n";
                return true;
            }

            // Sample blog posts
            $sampleBlogs = [
                [
                    'title' => 'Understanding SQL Joins',
                    'content' => 'SQL joins are used to combine rows from two or more tables based on a related column between them. The most common types of joins are INNER JOIN, LEFT JOIN, RIGHT JOIN, and FULL OUTER JOIN.',
                    'author_id' => 1,
                    'is_published' => 1
                ],
                [
                    'title' => 'A Guide to Python Decorators',
                    'content' => 'Decorators in Python are a powerful tool that allows you to modify the behavior of a function or class. They are often used for logging, enforcing access control, instrumentation, caching, and more.',
                    'author_id' => 2,
                    'is_published' => 1
                ],
                [
                    'title' => 'Exploring JavaScript Promises',
                    'content' => 'JavaScript promises are objects that represent the eventual completion (or failure) of an asynchronous operation and its resulting value. They provide a cleaner alternative to traditional callback-based approaches.',
                    'author_id' => 1,
                    'is_published' => 0
                ]
            ];

            foreach ($sampleBlogs as $blog) {
                $result = $db->execute(
                    "INSERT INTO blogs (title, content, author_id, is_published) VALUES (?, ?, ?, ?)",
                    [$blog['title'], $blog['content'], $blog['author_id'], $blog['is_published']]
                );
                echo "📝 Created blog: {$blog['title']}\n";
            }

            // Sample tasks
            $sampleTasks = [
                ['name' => 'Write blog about PHP', 'description' => 'Create comprehensive PHP tutorial', 'user_id' => 1, 'status' => 'In progress'],
                ['name' => 'Review database design', 'description' => 'Check normalization and indexes', 'user_id' => 1, 'status' => 'Not started'],
                ['name' => 'Deploy to production', 'description' => 'Deploy latest changes to live server', 'user_id' => 2, 'status' => 'completed']
            ];

            foreach ($sampleTasks as $task) {
                $db->execute(
                    "INSERT INTO tasks (name, description, user_id, status) VALUES (?, ?, ?, ?)",
                    [$task['name'], $task['description'], $task['user_id'], $task['status']]
                );
                echo "✅ Created task: {$task['name']}\n";
            }

            // Link blogs to categories
            $blogCategories = [
                [1, 1], // Blog 1 -> SQL
                [2, 2], // Blog 2 -> Python
                [3, 3], // Blog 3 -> JavaScript
                [3, 4]  // Blog 3 -> Web Development
            ];

            foreach ($blogCategories as $link) {
                $db->execute(
                    "INSERT IGNORE INTO blog_categories (blog_id, category_id) VALUES (?, ?)",
                    $link
                );
            }

            echo "✅ Sample data seeded successfully!\n";
            return true;

        } catch (Exception $e) {
            echo "❌ Seeding failed: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Show help information
     */
    public function help() {
        echo "🗄️  Database Migration System\n";
        echo "============================\n\n";
        echo "Usage: php migrate.php [command]\n\n";
        echo "Commands:\n";
        echo "  migrate  - Run all pending migrations (default)\n";
        echo "  status   - Show database status and migration history\n";
        echo "  backup   - Create database backup\n";
        echo "  fresh    - Drop all tables and run fresh migrations\n";
        echo "  seed     - Add sample data to database\n";
        echo "  help     - Show this help message\n\n";
        echo "Examples:\n";
        echo "  php migrate.php migrate\n";
        echo "  php migrate.php status\n";
        echo "  php migrate.php backup my_backup.sql\n";
        echo "  php migrate.php fresh\n";
        echo "  php migrate.php seed\n";
    }

    private function showStatus() {
        $this->migration->showStatus();
    }
}

// Main execution
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $runner = new MigrationRunner();

    $command = $argv[1] ?? 'migrate';

    switch ($command) {
        case 'migrate':
            $success = $runner->migrate();
            exit($success ? 0 : 1);

        case 'status':
            $runner->status();
            break;

        case 'backup':
            $filename = $argv[2] ?? null;
            $success = $runner->backup($filename);
            exit($success ? 0 : 1);

        case 'fresh':
            $success = $runner->fresh();
            exit($success ? 0 : 1);

        case 'seed':
            $success = $runner->seed();
            exit($success ? 0 : 1);

        case 'help':
        case '--help':
        case '-h':
            $runner->help();
            break;

        default:
            echo "❌ Unknown command: $command\n\n";
            $runner->help();
            exit(1);
    }
}
?>
