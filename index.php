<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
include "./components/header.php";
?>
    <div class="hero">
        <div class="hero-content">
            <h1>Welcome to Resonex</h1>
            <p>Securely login and manage your tasks & blogs with us</p>
            <div class="hero-buttons">

                <?php if(isLoggedIn()) : ?>
                    <a class="btn" href="todo.php">App</a>
                    <a class="btn" href="logout.php">Logout</a>

                <?php else:  ?>
                    <a class="btn" href="login.php">Login</a>
                    <a class="btn" href="register.php">Register</a>

                <?php endif; ?>

            </div>
        </div>
    </div>
<?php include "./components/footer.php"; ?>
