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

            <h2>Update Blog</h2>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="text" name="title" value="data value will be here" placeholder="Title" required>
                <br>

                <input type="text" name="author" value="data value will be here" placeholder="Author" required>
                <br>

                <input type="text" name="tags" value="data value will be here" placeholder="Tags (comma separated)" required>
                <br>

                <input type="date" name="created_date" required>
                <br>

                <input type="url" name="image" value="data value will be here" placeholder="Enter image URL" required>
                <br>


                <textarea name="content" placeholder="Content" required>data value will be here</textarea>
                <br>

                <input type="submit" value="Update Blog">
                <a href="blog.php">Cancel</a>
            </form>
        </div>
    </div>
</div>


<script src="assets/js/gradient.js"></script>

<?php include "components/footer.php"; ?>
