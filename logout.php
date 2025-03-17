<?php

include "./components/header.php";

session_start();

$_SESSION = [];

session_destroy();

redirect("index.php");

?>
