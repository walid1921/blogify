<div class="container">
    <canvas id="gradient-canvas"></canvas>

    <div class="blog-page">
        <?php if (!empty($blogs)): ?>

            <div class="filter-bar">
                <div class="filters">
                    <button class="filter-btn active"><?php echo $totalBlogs; ?> Blogs</button>
                    <?php foreach ($categories as $category): ?>
                        <button class="filter-btn"><?php echo htmlspecialchars($category['name']); ?></button>
                    <?php endforeach; ?>
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
                                    <?php if (!empty($blog['category_names'])): ?>
                                        <?php foreach (explode(', ', $blog['category_names']) as $cat): ?>
                                            <span class="tag"><?php echo htmlspecialchars($cat); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="tag">Uncategorized</span>
                                    <?php endif; ?>

                                </div>

                                <div class="author">
                                    <span class="author-name">Author ID: <?php echo htmlspecialchars($blog['author_id']); ?></span>
                                    <span class="created-date"><?php echo date('d M Y', strtotime($blog['created_at'])); ?></span>
                                </div>

                                <p><?php echo $blog['preview']; ?></p>

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
        <?php else: ?>
            <?php include __DIR__ . '/../components/emptyPage.php'; ?>
        <?php endif; ?>
    </div>
</div>
