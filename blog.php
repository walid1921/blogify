<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
include "./components/header.php";
?>

<main class="container">

    <div class="filter-bar">
        <div class="filters">
            <button class="filter-btn active">All</button>
            <button class="filter-btn">Product</button>
            <button class="filter-btn">Tools</button>
            <button class="filter-btn">Software Development</button>
            <button class="filter-btn">Design</button>
            <button class="filter-btn">Marketing</button>
        </div>
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

                    <button class="blog-btn">
                        <a  href="article.php?id=<?php echo $i; ?>">Read Post</a>

                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
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

</main>

<?php include "./components/footer.php"; ?>
