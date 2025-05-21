<?php
global $pdo;
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "includes/crudUser.php";

// Check if user is logged in and is an admin
if (!isLoggedIn()) {
    redirect("login.php");
}


include "./components/header.php";
include './components/usersTable.php';
include "./components/footer.php";
