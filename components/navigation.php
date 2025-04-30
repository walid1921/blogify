

<nav class="navbar">

    <a href="index.php" class="logo">Blogify</a>

    <ul class="menu">
        <?php if (isLoggedIn() && !isAdmin()) : ?>
            <li><a class="<?php echo activeLink('todo.php') ?>" href="todo.php">Tasks</a></li>
            <li><a class="<?php echo activeLink('profile.php') ?>" href="profile.php">Profile</a></li>
        <?php elseif (isLoggedIn() && isAdmin()) : ?>
            <li><a class="<?php echo activeLink('todo.php') ?>" href="todo.php">Tasks</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="blog.php">Blogs</a></li>
            <li><a class="<?php echo activeLink('admin.php') ?>" href="admin.php">Admin</a></li>
            <li><a class="<?php echo activeLink('profile.php') ?>" href="profile.php">Profile</a></li>
        <?php else : ?>
            <li><a class="<?php echo activeLink('index.php') ?>" href="index.php">Home</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="blog.php">Blogs</a></li>
            <li><a href="about">About</a></li>
            <li><a class="<?php echo activeLink('contact.php') ?>" href="contact.php">Contact</a></li>
        <?php endif; ?>
    </ul>
    <div class="nav-right">
        <?php if (isLoggedIn()) : ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else : ?>
            <li><a class="<?php echo activeLink('login.php') ?> primary-button nav-btn" href="login.php">Login</a></li>
        <?php endif; ?>
    </div>
    <div class="hamburger">
        <div class="bar1"></div>
        <div class="bar2"></div>
        <div class="bar3"></div>
    </div>
</nav>
<nav class="mobileNav">
    <ul class="menu">
        <?php if (isLoggedIn() && !isAdmin()) : ?>
            <li><a class="<?php echo activeLink('todo.php') ?>" href="todo.php">Tasks</a></li>
            <li><a class="<?php echo activeLink('profile.php') ?>" href="profile.php">Profile</a></li>
        <?php elseif (isLoggedIn() && isAdmin()) : ?>
            <li><a class="<?php echo activeLink('todo.php') ?>" href="todo.php">Tasks</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="blog.php">Blogs</a></li>
            <li><a class="<?php echo activeLink('admin.php') ?>" href="admin.php">Admin</a></li>
            <li><a class="<?php echo activeLink('profile.php') ?>" href="profile.php">Profile</a></li>
        <?php else : ?>
            <li><a class="<?php echo activeLink('index.php') ?>" href="index.php">Home</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="blog.php">Blogs</a></li>
            <li><a href="about">About</a></li>
            <li><a class="<?php echo activeLink('contact.php') ?>" href="contact.php">Contact</a></li>
        <?php endif; ?>

        <div class="mobile-nav-right">
            <?php if (isLoggedIn()) : ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else : ?>
                <li><a class="<?php echo activeLink('login.php') ?> primary-button nav-btn" href="login.php">Login</a></li>
            <?php endif; ?>
        </div>
    </ul>
</nav>
