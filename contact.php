<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
include "./components/header.php";
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


        <!--    --><?php
        //    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //        // Handle form submission
        //        $name = $_POST["name"];
        //        $email = $_POST["email"];
        //        $message = $_POST["message"];
        //
        //        // Here you would typically send the email or save the message to a database
        //        echo "<p>Thank you, $name! Your message has been sent.</p>";
        //    }
        //    ?>
    </div>


</div>

<script src="/assets/js/gradient.js"></script>


<?php include "./components/footer.php"; ?>
