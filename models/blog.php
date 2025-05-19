<?php

require_once __DIR__ . '/../config/database.php';


class Blog {
    private $pdo;
    private $table = "blog";
    public $id;
    public $author_id;
    public $title;
    public $content;
    public $created_at;
    public $is_published;

    public function __construct() {
        $db = new Database();
        $this->pdo  = $db->getConnection();
    }

    //! fetch all blogs for admin
    public function getAllBlogsForAdmin() {
        $query = "SELECT * FROM blogs ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //! fetch all blog posts for the current user
    public function getAllBlogs() {
        $query = "SELECT * FROM blogs WHERE author_id = :author_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':author_id' => $_SESSION['user_id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
