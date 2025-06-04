
<!--Desktop Navbar-->
<nav class="navbar">
    <a href="/index.php" class="logo">Blogify</a>

    
    <ul class="menu">
        <?php if (isLoggedIn() && !isAdmin()) : ?>
            <li><a class="<?php echo activeLink('todo.php') ?>" href="/todo.php">Planner</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="/blog.php">Blogs</a></li>
        <?php elseif (isLoggedIn() && isAdmin()) : ?>
            <li><a class="<?php echo activeLink('todo.php') ?>" href="/todo.php">Planner</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="/blog.php">Blogs</a></li>
            <li><a class="<?php echo activeLink('users.php') ?>" href="/users.php">Users</a></li>
        <?php else : ?>
            <li><a class="<?php echo activeLink('index.php') ?>" href="/index.php">Home</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="/blog.php">Blogs</a></li>
            <li><a href="/about">About</a></li>
            <li><a class="<?php echo activeLink('contact.php') ?>" href="/contact.php">Contact</a></li>
        <?php endif; ?>
    </ul>
    <div class="nav-right">
        <?php if (isLoggedIn()) : ?>
            <a class="user-btn" href="/profile.php"><i class="fa-solid fa-user fa-fw"></i></a>
            <a class="<?php echo activeLink('logout.php') ?> primary-button nav-btn with-icon" href="/logout.php">Logout <i class="fa-solid fa-arrow-right-from-bracket"></i></a>
        <?php else : ?>
            <a class="<?php echo activeLink('login.php') ?> primary-button nav-btn" href="/login.php">Login</a>
            <a class="<?php echo activeLink('register.php') ?> secondary-button nav-btn" href="/register.php">Register</a>
        <?php endif; ?>
    </div>
    <div class="hamburger">
        <div class="bar1"></div>
        <div class="bar2"></div>
        <div class="bar3"></div>
    </div>
</nav>

<!--  Mobile Navbar-->
<nav class="mobileNav">
    <ul class="menu">
        <?php if (isLoggedIn() && !isAdmin()) : ?>
            <li><a class="<?php echo activeLink('todo.php') ?>" href="/todo.php">Planner</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="/blog.php">Blogs</a></li>
        <?php elseif (isLoggedIn() && isAdmin()) : ?>
            <li><a class="<?php echo activeLink('todo.php') ?>" href="/todo.php">Planner</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="/blog.php">Blogs</a></li>
            <li><a class="<?php echo activeLink('users.php') ?>" href="/users.php">Users</a></li>
        <?php else : ?>
            <li><a class="<?php echo activeLink('index.php') ?>" href="/index.php">Home</a></li>
            <li><a class="<?php echo activeLink('blog.php') ?>" href="/blog.php">Blogs</a></li>
            <li><a href="/about">About</a></li>
            <li><a class="<?php echo activeLink('contact.php') ?>" href="/contact.php">Contact</a></li>
        <?php endif; ?>

        <div class="mobile-nav-right">
            <?php if (isLoggedIn()) : ?>
                <li><a class="user-btn" href="/profile.php"><i class="fa-solid fa-user fa-fw"></i></a></li>
                <li><a class="with-icon" href="/logout.php">Logout <i class="fa-solid fa-arrow-right-from-bracket"></i></a></li>
            <?php else : ?>
                <li><a class="<?php echo activeLink('login.php') ?> primary-button nav-btn" href="/login.php">Login</a></li>
                <li><a class="<?php echo activeLink('register.php') ?> secondary-button nav-btn" href="/register.php">Register</a></li>
            <?php endif; ?>
        </div>
    </ul>
</nav>
