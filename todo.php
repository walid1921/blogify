<?php
require_once __DIR__ . '/controllers/taskController.php';


include "./components/header.php";

$controller = new TaskController();
$controller->index();

?>


<?php include "./components/footer.php"; ?>
