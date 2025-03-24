<?php 

include "./components/header.php";

if (!isLoggedIn()) {
    redirect("index.php");
}

?>


    <div class="container">
       
        <h1>A To Do List App will be here</h1>
       
    </div>


<?php include "./components/footer.php"; ?>