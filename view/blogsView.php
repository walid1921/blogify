<div class="container">
    <canvas id="gradient-canvas"></canvas>

    <?php if(isset($_SESSION["message"])): ?>
        <div class="notification-container">
            <div class="notification <?php echo $_SESSION["msg_type"]?>">
                <!-- Success message will go here -->
                <?php echo  $_SESSION["message"];?>
                <?php unset($_SESSION["message"]);?>
            </div>
        </div>
    <?php endif; ?>

    <div class="blog-page">
        <?php if (!empty($blogs)): ?>

            <div class="filter-bar">
                    <div class="filters">
                        <button class="filter-btn active">Filter</button>

                        <?php foreach ($categories as $category): ?>
                            <button class="filter-btn"><?php echo htmlspecialchars($category['name']); ?></button>
                        <?php endforeach; ?>
                    </div>

                <?php if(isLoggedIn() && !isAdmin()):?>
                    <button class="filter-btn">
                        <a href="blogs_manager.php">
                            New Blog
                            <i class=" fa-solid fa-plus fa-md"></i>
                        </a>
                    </button>
                <?php endif;?>
            </div>

        <?php if (isLoggedIn() && isAdmin()): ?>
                <div class="counts">
                    <span class="published"><?php echo $totalBlogs['total_blogs']; ?> Published Blogs</span>
                </div>
        <?php elseif (isLoggedIn() && !isAdmin()): ?>
                <div class="counts">
                    <span class="published"><?php echo $totalBlogs['published_blogs']; ?> Published Blogs</span>
                    <span class="pending"><?php echo $totalBlogs['pending_blogs']; ?> Pending Blogs</span>
                </div>
        <?php endif; ?>

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
                                    <span class="author-name">Author: <?php echo htmlspecialchars($blog['username']); ?></span>
                                    <span class="created-date"><?php echo date('d M Y', strtotime($blog['created_at'])); ?></span>
                                </div>

                                <p><?php echo $blog['content']; ?></p>


                                <?php if(isLoggedIn() && !isAdmin()):?>
                                    <div class="blog-footer">
                                        <button class="primary-button">
                                            Read Blog
                                        </button>
                                        <form method="POST" action="/blog.php?action=updateBlog" class="publish-form">
                                            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                            <input type="hidden" name="is_published" value="<?php echo $blog['is_published'] ? '0' : '1'; ?>">
                                            <button type="submit"
                                                    class="status-btn  <?php echo $blog['is_published'] ? 'published' : 'pending'; ?>"
                                                    data-current="<?php echo $blog['is_published'] ? 'published' : 'pending'; ?>">
                                                <?php echo $blog['is_published'] ? 'Published' : 'Pending'; ?>
                                            </button>
                                        </form>




                                    </div>
                                <?php elseif(!isLoggedIn() || isAdmin()):?>
                                    <button class="primary-button with-icon">
                                        Read Blog
                                    </button>
                                <?php endif;?>



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
