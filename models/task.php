<?php

require_once __DIR__ . '/../config/database.php';


class Task {
        private $pdo;
        private $table = "tasks";
        public $id;
        public $task;
        public $status;
        public $created_at;
        public $is_completed;


        public function __construct() {
            $db = new Database();
            $this->pdo  = $db->getConnection();
        }

        //! fetch tasks for the current user
        public function getAllTasks() {
            $query = "SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //!
        public function getTaskById($id) {
            $query = "SELECT * FROM $this->table WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        //! create a new task for the current user
        public function createTask() {
            $query = "INSERT INTO $this->table (task, user_id) VALUES (:task, :user_id)";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                    ':task' => $this->task,
                    ':user_id' => $_SESSION['user_id']
            ]);
        }

        //! update task
        public function complete($id) {
            // to test : var_dump($id);

            $query = "UPDATE $this->table SET is_completed = 1 WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([':id' => $id]);
        }


        public function undoComplete($id) {
            $query = "UPDATE $this->table SET is_completed = 0 WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([':id' => $id]);
        }

        //! delete task
        public function deleteTask($id) {
            $query = "DELETE FROM $this->table WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([':id' => $id]);
        }





}
