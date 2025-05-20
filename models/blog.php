<?php

require_once __DIR__ . '/../config/database.php';

class Blog {
    private $pdo;
    private $table = "blogs"; // corrected from "blog"

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

    //! Fetch all blogs for admin (all authors)
    public function getAllBlogsWithCategoriesForAdmin() {
        $query = "SELECT b.*, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names
        FROM blogs b
        LEFT JOIN blog_categories bc ON b.id = bc.blog_id
        LEFT JOIN categories c ON bc.category_id = c.id
        GROUP BY b.id, b.title, b.content, b.author_id, b.created_at, b.is_published, b.updated_at
        ORDER BY b.created_at DESC";

        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }


    //! Fetch blogs by current logged-in user (author)
    public function getBlogsWithCategoriesByAuthor($author_id) {
        $query = "SELECT b.*, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names
        FROM blogs b
        LEFT JOIN blog_categories bc ON b.id = bc.blog_id
        LEFT JOIN categories c ON bc.category_id = c.id
        WHERE b.author_id = :author_id
        GROUP BY b.id, b.title, b.content, b.author_id, b.created_at, b.is_published, b.updated_at
        ORDER BY b.created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':author_id' => $author_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //! Get all categories
    public function getCategories() {
        $query = "SELECT * FROM categories";
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    //! Get number of blogs for admin
    public function getNumberOfBlogs() {
        $query = "SELECT COUNT(*) as total_blogs FROM blogs";
        $stmt = $this->pdo->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_blogs'];
    }

    //! Get number of blogs for author
    public function getNumberOfBlogsByAuthor($author_id) {
        $query = "SELECT COUNT(*) as total_blogs FROM blogs WHERE author_id = :author_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':author_id' => $author_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_blogs'];
    }
}
