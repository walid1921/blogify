<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "components/toast.php";
require_once __DIR__ . '/../models/blog.php';


class BlogController {


    public function index() {

        $currentUser = $_SESSION['username'];



        $blogModel = new Blog();


        // Check if the user is an admin
        if (isAdmin()) {
            // Fetch all blogs for admin
            $blogs = $blogModel->getAllBlogsForAdmin();
        } else {
            // Fetch only the blogs for the current user
            $blogs = $blogModel->getAllBlogs();
        }

        // to display the blogs
        // echo "<pre>";
        // var_dump($blogs);
        // echo "</pre>";



        if(!isLoggedIn()) {
            redirect("login.php");
        }





        // Pass Blogs data to the view
        require __DIR__ . '/../view/blogsView.php';


    }

}
