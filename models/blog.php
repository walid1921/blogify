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

    //! Fetch all published blogs (all authors) for admin, guest pages
    public function getAllBlogsWithCategories() {
        $query = "SELECT b.id, b.title, b.content, b.author_id, b.created_at, b.updated_at, b.is_published, u.username, 
        GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names
        FROM blogs b
            LEFT JOIN blog_categories bc ON b.id = bc.blog_id
            LEFT JOIN categories c ON bc.category_id = c.id
            LEFT JOIN users u ON b.author_id = u.id
        WHERE b.is_published = 1
        GROUP BY b.id, b.title, b.content, b.author_id, b.created_at, b.updated_at, b.is_published, u.username
        ORDER BY b.created_at DESC";

        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }


    //! Fetch blogs (published, pending) for current user (author)
    public function getBlogsWithCategoriesByAuthor($author_id) {
        $query = "
    SELECT b.id, b.title, b.content, b.author_id, b.created_at, b.updated_at, b.is_published, u.username,
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

    //! Get number of published blogs for the guest & admin pages
    public function getNumberOfBlogs() {
        $query = "SELECT COUNT(*) as total_blogs FROM blogs WHERE is_published = 1";
        $stmt = $this->pdo->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    //! Get number of published and pending blogs for current user
    public function getBlogStatusCountsByAuthor($author_id) {
        $query = "SELECT 
        SUM(CASE WHEN is_published =1 THEN 1 ELSE 0 END) AS published_blogs,
        SUM(CASE WHEN is_published =0 THEN 1 ELSE 0 END) AS pending_blogs
        FROM blogs WHERE author_id = :author_id" ;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':author_id' => $author_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //! Update blog to published or pending
    public function updateBlogStatus($id, $status) {
        $query = "UPDATE blogs SET is_published = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    //! Delete a blog for the current user or Admin can delete any blog
     function deleteBlogData($id, $author_id = null) {
         // First delete from blog_categories to avoid foreign key constraint
         $this->deleteBlogCategories($id);

        if ($author_id) {
            // Regular user can only delete their own blogs
            $query = "DELETE FROM blogs WHERE id = :id AND author_id = :author_id";
            $params = [':id' => $id, ':author_id' => $author_id];
        } else {
            // Admin can delete any blog
            $query = "DELETE FROM blogs WHERE id = :id";
            $params = [':id' => $id];
        }

        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);

         // to display the blogs
//          echo "<pre>";
//          var_dump($id);
//          echo "</pre>";

    }

    public function deleteBlogCategories($blogId) {
        $query = "DELETE FROM blog_categories WHERE blog_id = :blog_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':blog_id' => $blogId]);
    }
}
