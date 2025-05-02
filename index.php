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
                    <h1>Blogify. Your Blog Management Platform</h1>
                    <p>
                        A lot of users are using our platform to manage their blogs. We provide a secure and easy way to manage blogs.
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
        <div class="about-blogify" id="about">
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
                <button class="primary-button with-icon">
                    Start Blogging
                    <img src="assets/images/arrow-right-light.svg" alt="">
                </button>
            </div>
        </div>

        <!-- !  Designed-Engineers SECTION   -->
        <div class="designed-section">
            <div class="section-container two-col">
                <div class="col-left">
                    <p class="subtitle-2">Designed For Engineers</p>
                    <h2>The world’s best and most intuitive APIs</h2>
                    <p>We abstract the hard stuff away so your teams don't can focus on building good technology, instead of
                        spending time and money reinventing the wheel.</p>
                    <button class="primary-button with-icon">Start now <img src="assets/images/arrow-right-dark.svg" alt=""></button>
                    <div class="card-container">
                        <div class="card">
                            <img src="assets/images/tools-icon.png" class="icon" alt="">
                            <h3>Tools for all stacks</h3>
                            <p class="p2">We offer front end and back end libraries in some of the most widely used technologies, old
                                and new.</p>
                            <button class="secondary-button with-icon">See libraries
                                <img src="assets/images/arrow-right-blue.svg" alt="">
                            </button>
                        </div>
                        <div class="card"><img src="assets/images/cube-icon.png" class="icon" alt="">
                            <h3>Custom Integrations</h3>
                            <p class="p2">Use integrations for systems like Shopify, WooCommerce, NetSuite, and more.</p>
                            <button class="secondary-button with-icon">Explore partners <img src="assets/images/arrow-right-blue.svg" alt=""></button>
                        </div>
                    </div>
                </div>
                <div class="col-right"><img id="api-code" src="assets/images/api-code.png" alt="">
                    <img id="terminal"  src="assets/images/terminal-code.png" alt=""></div>


            </div>
        </div>

        <!-- ! Why Blogify SECTION -->
        <div class="why-blogify-section">
            <div class="section-container">
                <p class="subtitle">Why Blogify</p>
                <h2>A platform built for creators</h2>
                <div class="card-container">
                    <div class="card">
                        <img src="assets/images/cloud-icon.png" class="icon" alt="">
                        <h3>Write Anywhere</h3>
                        <p class="p2">Blogify is cloud-based and mobile-friendly, so you can draft, edit, and publish from any device, anytime.</p>
                    </div>
                    <div class="card">
                        <img src="assets/images/cycle-icon.png" class="icon" alt="">
                        <h3>Constantly Evolving</h3>
                        <p class="p2">We release regular updates with new features, better tools, and performance improvements — all based on user feedback.</p>
                    </div>
                    <div class="card">
                        <img src="assets/images/shield-icon.png" class="icon" alt="">
                        <h3>Secure & Reliable</h3>
                        <p class="p2">Your content is safe with us. We offer encrypted storage, automatic backups, and consistent uptime.</p>
                    </div>
                    <div class="card">
                        <img src="assets/images/bars-icon.png" class="icon" alt="">
                        <h3>Optimized for Reach</h3>
                        <p class="p2">Built-in SEO tools, performance analytics, and social integrations help you grow your audience faster than ever.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ! Contact SECTION -->
        <div class="global-section">
            <div class="section-container">
                <div class="two-col">
                    <div class="col-left">
                        <p class="subtitle-2">Get in Touch</p>
                        <p>Have questions about starting your blog, need help with features, or want to partner with us? We’d love to hear from you.</p>

                        <div class="form-container blur-bg">
                            <form action="contact.php" method="POST">
                                <input type="text" id="name" name="name" placeholder="Your Name" required><br>
                                <input type="email" id="email" name="email" placeholder="Your Email" required><br>
                                <textarea id="message" name="message" placeholder="Write your message here..." required></textarea><br>
                                <input type="submit" value="Send Message">
                            </form>
                        </div>
                    </div>
                    <div class="col-right">
                        <img id="globe" src="assets/images/global-graphic.png" alt="Global reach illustration">
                    </div>
                </div>

                <div class="card-container">
                    <div class="card">
                        <h3>50K+</h3>
                        <p class="p2">active bloggers sharing content every day</p>
                    </div>
                    <div class="card">
                        <h3>120+</h3>
                        <p class="p2">countries with active Blogify users</p>
                    </div>
                    <div class="card">
                        <h3>99.9%</h3>
                        <p class="p2">uptime ensuring your blog is always accessible</p>
                    </div>
                    <div class="card">
                        <h3>24/7</h3>
                        <p class="p2">support to help you with your blog whenever you need it</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- !  COMMUNITY SECTION   -->
        <div class="virtual-section">
            <div class="section-container two-col">
                <div class="col-left">
                    <p class="subtitle">Community</p>
                    <h2>Join the Blogify community</h2>
                    <p>Join our community of bloggers, writers, and content creators. Share your experiences, get tips, and connect with like-minded individuals.</p>
                    <button class="primary-button with-icon">Join now<img src="assets/images/arrow-right-light.svg" alt=""></button>
                </div>
                <div class="col-right">
                    <div class="blogify-card">
                        <div class="card-top">
                            <span class="blogify">BLOGIFY SESSIONS</span>
                            <div class="avatars">
                                <img src="assets/images/avatar1.png" alt="">
                                <img src="assets/images/avatar2.png" alt="">
                            </div>
                        </div>
                        <div class="card-bottom">
                            <div class="bottom-content">
                                <p class="subtitle">Keynote</p>
                                <p >
                                    Join us for our keynote session where we will discuss the future of blogging and content creation.
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ! Waiting SECTION -->
        <div class="waiting-section">
            <div class="section-container two-col">
                <div class="col-left">
                    <h2>What are you waiting for?</h2>
                    <p>Discover the power of <a href="#">Blogify</a>, or sign up now and start sharing your stories with the world! Whether you're a beginner or a pro, we're here to support your blogging journey.</p>
                    <button class="primary-button with-icon">Start Blogging <img src="assets/images/arrow-right-light.svg" alt=""></button>
                </div>
                <div class="col-right">
                    <div class="card-container">
                        <div class="card">
                            <img src="assets/images/shield-icon.png" class="icon" alt="">
                            <h3>Your words, your control</h3>
                            <p class="p2">You own your content — export anytime, no hidden limitations or fees.</p>
                            <button class="secondary-button with-icon">Learn more <img src="assets/images/arrow-right-purple.svg" alt=""></button>
                        </div>
                        <div class="card">
                            <img src="assets/images/bars-icon.png" class="icon" alt="">
                            <h3>Publish in minutes</h3>
                            <p class="p2">Create and publish your first post with our user-friendly editor — no setup required.</p>
                            <button class="secondary-button with-icon">Editor guide <img src="assets/images/arrow-right-purple.svg" alt=""></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--! Footer -->
        <div class="footer">
            <div class="section-container">
                <div class="col">
                    <a href="index.php" class="logo">Blogify</a>
                </div>
                <div class="col">
                    <h3>Features</h3>
                    <ul>
                        <li><a href="#">Rich Text Editor</a></li>
                        <li><a href="#">Themes & Customization</a></li>
                        <li><a href="#">Post Scheduling</a></li>
                        <li><a href="#">SEO Tools</a></li>
                        <li><a href="#">Analytics</a></li>
                        <li><a href="#">Content Backup</a></li>
                    </ul>
                </div>
                <div class="col">
                    <h3>Use Cases</h3>
                    <ul>
                        <li><a href="#">Personal Blogging</a></li>
                        <li><a href="#">Professional Portfolios</a></li>
                        <li><a href="#">News & Media</a></li>
                        <li><a href="#">Tech Writing</a></li>
                        <li><a href="#">Lifestyle & Travel</a></li>
                        <li><a href="#">Team Blogs</a></li>
                    </ul>
                </div>
                <div class="col">
                    <h3>Resources</h3>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Getting Started Guide</a></li>
                        <li><a href="#">Blogify Blog</a></li>
                        <li><a href="#">Community Forum</a></li>
                        <li><a href="#">Privacy & Terms</a></li>
                        <li><a href="#">Cookie Preferences</a></li>
                    </ul>
                </div>
            </div>
        </div>

    </main>


    <script src="/assets/js/gradient.js"></script>
<!--    <script src="/assets/js/modal.js"></script>-->


<?php include "./components/footer.php"; ?>
