<?php
require_once __DIR__ . '/controllers/loginController.php';
require_once __DIR__ . '/includes/helpers.php';

if (isLoggedIn()) {
redirect('todo.php');
}

$controller = new LoginController();
$controller->handleRequest();
