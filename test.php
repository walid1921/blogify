<?php

require_once __DIR__ . '/controllers/userController.php';

// Instantiate controller and run the index method
$controller = new UserController();
$controller->index();
