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
    public function getAllBlogsWithCategories() {
        $query = "SELECT b.*, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names
        FROM blogs b
        LEFT JOIN blog_categories bc ON b.id = bc.blog_id
        LEFT JOIN categories c ON bc.category_id = c.id
        WHERE b.is_published = 1
        GROUP BY b.id, b.title, b.content, b.author_id, b.created_at, b.is_published
        ORDER BY b.created_at DESC";

        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }


    //! Fetch blogs by current logged-in user (author)
    public function getBlogsWithCategoriesByAuthor($author_id) {
        $query = "SELECT
    b.id, b.title, b.content, b.author_id, b.created_at, b.updated_at, b.is_published,
    u.username,
    GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names
FROM blogs b
         LEFT JOIN blog_categories bc ON b.id = bc.blog_id
         LEFT JOIN categories c ON bc.category_id = c.id
         LEFT JOIN users u ON b.author_id = u.id
WHERE b.author_id = :author_id
GROUP BY b.id, b.title, b.content, b.author_id, b.created_at, b.updated_at, b.is_published,
    u.username
ORDER BY b.created_at DESC;";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':author_id' => $author_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //! Get all categories
    public function getCategories() {
        $query = "SELECT * FROM categories";
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    //! Get number of blogs for the landing page & admin
    public function getNumberOfBlogs() {
        $query = "SELECT COUNT(*) as total_blogs FROM blogs WHERE is_published = 1";
        $stmt = $this->pdo->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_blogs'];
    }


    //! Get number of published blogs for current logged-in user
    public function getNumberOfPublishedBlogsByAuthor($author_id) {
        $query = "SELECT COUNT(*) as total_blogs FROM blogs WHERE author_id = :author_id AND is_published = 1" ;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':author_id' => $author_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_blogs'];
    }

    //! Get num of pending blogs for current logged-in user
    public function getNumberOfPendingBlogsByAuthor($author_id) {
        $query = "SELECT COUNT(*) as total_blogs FROM blogs WHERE author_id = :author_id AND is_published = 0" ;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':author_id' => $author_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_blogs'];
    }

    //! Update blog to published or pending
    public function updateBlogStatus($id, $status) {
        $query = "UPDATE blogs SET is_published = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}
