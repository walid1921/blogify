<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../components/toast.php';
require_once __DIR__ . '/../models/blog.php';



class BlogController {

    public function index() {


        $blogModel = new Blog();

        //! Admin User
        if (isLoggedIn() && isAdmin()) {
            $blogs = $blogModel->getAllBlogsWithCategories(); // all blogs
            $totalBlogs = $blogModel->getNumberOfBlogs(); // total number of blogs

        //! Normal User
        } elseif (isLoggedIn() && !isAdmin()) {
            $blogs = $blogModel->getBlogsWithCategoriesByAuthor($_SESSION['user_id']); // only user's blogs
            $totalBlogs = $blogModel->getBlogStatusCountsByAuthor($_SESSION['user_id']);

        //! Guest User
        } else {
            $blogs = $blogModel->getAllBlogsWithCategories(); // all blogs
            $totalBlogs = $blogModel->getNumberOfBlogs(); // total number of blog
        }

        // to display the blogs
//         echo "<pre>";
//         var_dump($blogs);
//         echo "</pre>";

        $categories = $blogModel->getCategories();

        // Pass Blogs data to the view
        require __DIR__ . '/../view/blogsView.php';
    }

    //! method to handle the form submission for updating blog publish status
    public function updateBlog() {

        if(!isLoggedIn()) {
            redirect("login.php");
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $blogId = isset($_POST['blog_id']) ? $_POST['blog_id'] : null;
            // Checkbox sends value only if checked; if unchecked, treat as 0 (pending)
            $status = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;


            if ($blogId) {
                $blogModel = new Blog();
                $success = $blogModel->updateBlogStatus($blogId, $status);

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

    }


}
