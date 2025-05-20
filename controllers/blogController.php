<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "components/toast.php";
require_once __DIR__ . '/../models/blog.php';


class BlogController {

    public function index() {
        if (!isLoggedIn()) {
            redirect("login.php");
        }

        $blogModel = new Blog();

        // Determine which blogs to fetch
        if (isAdmin()) {
            $blogs = $blogModel->getAllBlogsWithCategoriesForAdmin(); // all blogs
            $totalBlogs = $blogModel->getNumberOfBlogs(); // total number of blogs

        } else {
            $blogs = $blogModel->getBlogsWithCategoriesByAuthor($_SESSION['user_id']); // only user's blogs
            $totalBlogs = $blogModel->getNumberOfBlogsByAuthor($_SESSION['user_id']);
        }

        // to display the blogs
//         echo "<pre>";
//         var_dump($blogs);
//         echo "</pre>";

        $categories = $blogModel->getCategories();

        // Pass Blogs data to the view
        require __DIR__ . '/../view/blogsView.php';
    }


}
