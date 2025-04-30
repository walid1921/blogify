<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
include "./components/header.php";
?>

    <main>
        <!-- GRADIENT BACKGROUND -->
        <canvas id="gradient-canvas"></canvas>

        <!-- HERO CONTENT   -->
        <div class="hero">
            <div class="section-container two-col">
                <div class="col-left">
                    <h1>Blogify. Your Blog & Task Management Platform</h1>
                    <p>
                        A lot of users are using our platform to manage their tasks and blogs. We provide a secure and easy way to manage your tasks and blogs.
                    </p>
                    <div class="btn-container">
                        <?php if(isLoggedIn()) : ?>
                               <a class="primary-button with-icon" href="todo.php">App <img src="/assets/images/arrow-right-light.svg" alt=""></a>
                               <a class="secondary-button with-icon" href="profile.php">Profile <img src="/assets/images/arrow-right-purple.svg" alt=""></a>
                        <?php else:  ?>
                                <a class="primary-button with-icon" href="login.php">Start now <img src="/assets/images/arrow-right-light.svg" alt=""></a>
                                <a class="secondary-button with-icon" href="blog.php">Blogs <img src="/assets/images/arrow-right-purple.svg" alt=""></a>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="col-right hero-phone-container">
                    <img class="hero-phone" src="/assets/images/hero-phone.png" alt="">
                </div>
            </div>
        </div>

        <!-- ABOUT SECTION -->
        <div class="unified-platform" id="about">
            <div class="section-container">
                <p class="subtitle">About Blogify</p>
                <h2>All the Blogging tools you'll ever need</h2>
                <div class="two-col">
                    <div class="col-left">
                        <p>Whether you're starting a personal journal, launching a professional blog, or building a community hub, Blogify offers everything you need — from beautiful themes to easy content management and audience engagement tools.</p>
                    </div>
                    <div class="col-right">
                        <p>We also provide features to <a href="#">optimize SEO</a>, <a href="#">schedule posts</a>, <a href="#">collaborate with co-authors</a>, <a href="#">analyze reader insights</a>, <a href="#">monetize your content</a>, and so much more.</p>
                    </div>
                </div>
                <button class="primary-button with-icon">Start Blogging <img src="assets/images/arrow-right-light.svg" alt=""></button>
            </div>
        </div>

        <!-- CONTACT SECTION -->

    </main>


    <script src="/assets/js/gradient.js"></script>
<!--    <script src="/assets/js/modal.js"></script>-->


<?php include "./components/footer.php"; ?>
