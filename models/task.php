<?php

class Task {
    private $pdo;

    public function __construct($pdo) {
       $db = new Database();
       $this->pdo = $db->getConnection();
    }

    public function getAllTasks() {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
