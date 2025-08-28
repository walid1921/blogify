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

            <div class="blog-header">
                <div class="header-content">
                    <h2>The freshest ecommerce insights, expert advice, and product news</h2>
                    <p>From global business trends and hot product news to community events and developer know-how. Get all the content you need to stay ahead.</p>
                </div>

                <img class="img-container" src="assets/images/meeting.jpg" alt="">

            </div>

            <div class="filter-bar">
                <div class="filters">
                    <button class="filter-btn active">All articles</button>

                    <?php foreach ($categories as $category): ?>
                        <button class="filter-btn">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php if(isLoggedIn() && !isAdmin()):?>
                    <button class="filter-btn active">
                        <a href="createBlog.php">
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
                        <div class="blog-card" id="blogCard">

                            <?php if((isAdmin() || $blog['author_id'] === $_SESSION['user_id'])): ?>
                                <button class="deleteBlog"
                                        data-blog-id="<?php echo $blog['id']; ?>"
                                        data-blog-title="<?php echo htmlspecialchars($blog['title']); ?>">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            <?php endif; ?>



                            <div class="blog-image-wrapper">
                                <img src="/assets/images/blog.webp" alt="Blog Post Image">

                                <?php
                                $createAt = new DateTime($blog['updated_at']);
                                $now = new DateTime();
                                $diff = $now->diff($createAt);

                                if($diff->days < 3){
                                    echo "<span class='new_tag'>New</span>";
                                }
                                ?>
                            </div>


                            <div class="card-content">

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
                                    <span class="created-date"><?php echo day_month_year($blog['created_at']); ?></span>
                                </div>

                                <div class="text-content">
                                    <h6><?php echo htmlspecialchars($blog['title']); ?></h6>
                                    <p><?php echo $blog['content']; ?></p>
                                </div>




                                <?php if(isLoggedIn() && !isAdmin()):?>
                                    <div class="blog-footer">
                                        <button class="primary-button">
                                            Read Blog
                                        </button>
                                        <form method="POST" action="/blog.php" class="publish-form">
                                            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                            <input type="hidden" name="is_published" value="<?php echo $blog['is_published'] ? '0' : '1'; ?>">
                                            <button name="update_blog" type="submit"
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


        <!--   Delete One blog Modal -->
        <div id="deleteOneBlogModal" class="modal deleteOneBlogModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Confirm Deletion</h4>
                    <span class="cancelBlogDeletion close">&times;</span>
                </div>
                <p>Are you sure you want to delete the blog titled:
                    <span id="blogTitleToDelete"></span> <br/>
                    <span class="permanently">permanently</span>?
                </p>

                <div class="deleteOneBlogModal-buttons">
                    <form method="POST" action="/blog.php?action=deleteBlog">
                        <input type="hidden" name="blog_id" id="deleteBlogId">
                        <button class="delete-button" type="submit" name="delete_blog">Delete</button>
                    </form>

                    <button class="cancelConfirmDeleteBlog cancelBlogDeletion">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="/assets/js/gradient.js"></script>
<script src="/assets/js/modal.js"></script>
