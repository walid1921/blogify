<?php
require_once __DIR__ . '/controllers/blogController.php';
include "components/header.php";

$blogController = new BlogController();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_blog'])) {
        $blogController->updateBlog();
    } elseif (isset($_POST['delete_blog'])) {
        $blogController->deleteBlog();
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'deleteBlog') {
    // Handle GET requests if needed
    $blogController->deleteBlog();
} else {
    $blogController->index();
}


include "components/footer.php";
