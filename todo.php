<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";

if(!isLoggedIn()) {
    redirect("login.php");
}

include "./components/header.php";
?>

    <div style="text-align:center; margin-top:60px; display:flex; flex-direction: column; gap:100px; ">

        <h1>A To Do List App will be here</h1>

    </div>


<?php include "./components/footer.php"; ?>