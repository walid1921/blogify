<?php
require_once __DIR__ . '/controllers/blogController.php';
include "./components/header.php";

$blogController = new BlogController();
$blogController->index();
?>


<script src="/assets/js/gradient.js"></script>

<?php include "./components/footer.php"; ?>
