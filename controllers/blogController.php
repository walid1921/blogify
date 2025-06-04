<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/blog.php';



class BlogController {

    private $blogModel;

    public function __construct() {
        $this->blogModel = new Blog();
    }

    public function index() {
        if (!isLoggedIn() || isAdmin()) {
            $blogs = $this->blogModel->getAllBlogsWithCategories();
            $totalBlogs = $this->blogModel->getNumberOfBlogs();

            // to display the blogs
            // echo "<pre>";
            // var_dump($blogs);
            // echo "</pre>";

        } else {
            $blogs = $this->blogModel->getBlogsWithCategoriesByAuthor($_SESSION['user_id']);
            $totalBlogs = $this->blogModel->getBlogStatusCountsByAuthor($_SESSION['user_id']);
        }

        $categories = $this->blogModel->getCategories();
        require __DIR__ . '/../view/blogsView.php'; // Pass Blogs data to the view
    }

    //! method to handle the form submission for updating blog publish status
    public function updateBlog() {

        if(!isLoggedIn()) {
            redirect("login.php");
        }


            $blogId = filter_input(INPUT_POST, 'blog_id', FILTER_VALIDATE_INT);
        // Checkbox sends value only if checked; if unchecked, treat as 0 (pending)
            $status = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;


            if ($blogId) {
                $success = $this->blogModel->updateBlogStatus($blogId, $status);

                if ($success) {
                    $_SESSION["message"] = "Blog status updated successfully!";
                    $_SESSION["msg_type"] = "success";
                } else {
                    $_SESSION["message"] = "Failed to update blog status.";
                    $_SESSION["msg_type"] = "error";
                }
            }

            redirect("blog.php");


    }

    //! method to handle the form submission for deleting a  blog
// In controllers/blogController.php
    public function deleteBlog() {
        if (!isLoggedIn()) {
            redirect("login.php");
        }

        $blogId = filter_input(INPUT_POST, 'blog_id', FILTER_VALIDATE_INT);

        if ($blogId) {
            // For admins, don't pass author_id
            $authorId = isAdmin() ? null : $_SESSION['user_id'];

            // Also delete from blog_categories table first to avoid foreign key constraint
            $this->blogModel->deleteBlogCategories($blogId);

            $success = $this->blogModel->deleteBlogData($blogId, $authorId);

            if ($success) {
                $_SESSION["message"] = "Blog deleted successfully!";
                $_SESSION["msg_type"] = "success";
            } else {
                $_SESSION["message"] = "Failed to delete blog.";
                $_SESSION["msg_type"] = "error";
            }
        }

        redirect("blog.php");
    }


}
