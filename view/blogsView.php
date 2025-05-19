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

            <?php foreach ($blogs as $blog): ?>
                <div class="blog">
                    <img src="/assets/images/laptop.jpg" alt="Blog Post Image">
                    <div class="content">
                        <h2><?php echo htmlspecialchars($blog['title']); ?></h2>

                        <div class="tags">
                            <span class="tag"><?php echo htmlspecialchars(isset($blog['category']) ? $blog['category'] : 'General'); ?></span>
                        </div>

                        <div class="author">
                            <span class="author-name">Author ID: <?php echo htmlspecialchars($blog['author_id']); ?></span>
                            <span class="created-date"><?php echo date('d M Y', strtotime($blog['created_at'])); ?></span>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($blog['content'])); ?></p>

                        <button class="primary-button with-icon">
                            Read Blog
                        </button>

                    </div>
                </div>
            <?php endforeach; ?>


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
