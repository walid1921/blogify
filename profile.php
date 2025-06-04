<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . "/controllers/profileController.php";

if (!isLoggedIn()) {
    redirect("login.php");
}

$controller = new ProfileController();
$controller->handleRequest();
