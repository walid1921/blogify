<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
include "components/header.php";
?>


<div>
    <canvas id="gradient-canvas"></canvas>


    <div class="hero">
        <div class="form-container">
            <h2>Get in Touch</h2>
            <p>Have questions about starting your blog, need help with features, or want to partner with us? We’d love to hear from you.</p>
            <form action="contact.php" method="POST">

                <input type="text" id="name" name="name" placeholder="name" required>
                <input type="email" id="email" name="email" placeholder="email" required>
                <textarea id="message" name="message" placeholder="write your message here" required></textarea>

                <input type="submit" value="Send">
            </form>
        </div>
    </div>


</div>

<script src="assets/js/gradient.js"></script>
<?php include "components/footer.php"; ?>
