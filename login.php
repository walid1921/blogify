<?php
require_once __DIR__ . '/controllers/loginController.php';
require_once __DIR__ . '/includes/helpers.php';

// If user is already logged in, redirect
if (isLoggedIn()) {
redirect('todo.php');
}

$controller = new LoginController();
$controller->handleRequest();
