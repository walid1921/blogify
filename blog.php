<?php
require_once __DIR__ . '/controllers/blogController.php';
include "./components/header.php";

$blogController = new BlogController();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'updateBlog') {
    $blogController->updateBlog();
} else {
    $blogController->index(); // default to blog list view
}
?>

<script src="/assets/js/gradient.js"></script>
<?php include "./components/footer.php"; ?>
