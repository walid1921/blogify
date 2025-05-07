<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
include "./components/header.php";
?>

<div class="container">

    <canvas id="gradient-canvas"></canvas>

    <div class="blog-page">
        <div class="filter-bar">
            <div class="filters">
                <button class="filter-btn active">All</button>
                <button class="filter-btn">Product</button>
                <button class="filter-btn">Tools</button>
                <button class="filter-btn">Software Development</button>
                <button class="filter-btn">Design</button>
                <button class="filter-btn">Marketing</button>

            </div>


            <button class="filter-btn with-icon">
                <a href="blogs_manager.php">
                    Blogs manager
                    <img src="/assets/images/arrow-right-light.svg" alt="">
                </a>
            </button>
        </div>

        <div class="blogs-wrapper">

            <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="blog">
                    <img src="./assets/images/laptop.jpg" alt="Blog Post Image">
                    <div class="content">
                        <h2>Blog Post Title <?php echo $i; ?></h2>

                        <div class="tags">
                            <span class="tag"><?php echo ($i % 2 == 0) ? 'Product' : 'Tools'; ?></span>
                            <?php if ($i % 3 == 0): ?><span class="tag">Software Development</span><?php endif; ?>
                            <?php if ($i % 4 == 0): ?><span class="tag">Design</span><?php endif; ?>
                        </div>

                        <div class="author">
                            <span class="author-name">Author <?php echo $i; ?></span>
                            <span class="created-date"><?php echo date('d M Y', strtotime("-$i days")); ?></span>
                        </div>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc vulputate libero et velit interdum, ac aliquet odio mattis.</p>

                        <button class="primary-button with-icon">
                            Read Blog
                            <!--  <a  href="article.php?id=--><?php //echo $i; ?><!--">Read Post</a>-->
                        </button>

                    </div>
                </div>
            <?php endfor; ?>

            <div class="pagination">
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <span class="dots">...</span>
                <a href="#" class="page-link">10</a>
                <a href="#" class="next-btn">Next →</a>
            </div>
        </div>
    </div>

</div>

<script src="/assets/js/gradient.js"></script>

<?php include "./components/footer.php"; ?>
