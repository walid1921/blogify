<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
include "components/header.php";

if (!isLoggedIn()) {
    redirect("login.php");
}
?>

<div>
    <canvas id="gradient-canvas"></canvas>

    <div class="hero">
        <div class="form-container">

            <h2>Create New Blog</h2>

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

                <div style="display: flex; justify-content: space-around; gap: 10px;">
                    <input type="submit" value="Create Blog">
                    <a class="cancelBtn" href="blog.php">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>


<script src="assets/js/gradient.js"></script>

<?php include "components/footer.php"; ?>
