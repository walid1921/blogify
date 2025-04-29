<nav class="navbar">
    <div class="nav-left">
        <a href="index.php" class="logo">Resonex</a>
    </div>

    <div class="nav-center">
        <ul class="nav-links">

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
                <li><a class="<?php echo activeLink('about.php') ?>" href="about.php">About</a></li>
                <li><a class="<?php echo activeLink('contact.php') ?>" href="contact.php">Contact</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="nav-right">
        <ul class="auth-links">
            <?php if (isLoggedIn()) : ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else : ?>
                <li><a class="<?php echo activeLink('register.php') ?>" href="register.php">Register</a></li>
                <li><a class="<?php echo activeLink('login.php') ?>" href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
