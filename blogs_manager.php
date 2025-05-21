<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
include "./components/header.php";

if (!isLoggedIn()) {
    redirect("login.php");
}
?>

<div>
    <canvas id="gradient-canvas"></canvas>

    <div class="hero">
        <div class="form-container">

            <h2>Blogs Manager</h2>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="text" name="title" placeholder="Title" required>
                <br>

                <input type="text" name="author" placeholder="Author" required>
                <br>

                <input type="text" name="tags" placeholder="Tags (comma separated)" required>
                <br>

                <input type="date" name="created_date" required>
                <br>

                <input type="url" name="image" placeholder="Enter image URL" required>
                <br>


                <textarea name="content" placeholder="Content" required></textarea>
                <br>

                <input type="submit" value="Create Blog">
            </form>
        </div>
    </div>
</div>


<script src="/assets/js/gradient.js"></script>

<?php include "./components/footer.php"; ?>
