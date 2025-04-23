<nav>
    <ul>
        <li>
            <a class="<?php echo activeLink("index.php") ?>" href="index.php">Home</a>
        </li>

        <?php if(isLoggedIn() && !isAdmin()) : ?>

            <li>
                <a class="<?php echo activeLink("todo.php") ?>" href="todo.php">Tasks</a>
            </li>
            <li>
                <a class="<?php echo activeLink("profile.php") ?>" href="profile.php">Profile</a>
            </li>
            <li>
                <a href="logout.php">Logout</a>
            </li>

        <?php elseif(isLoggedIn() && isAdmin()): ?>
            <li>
                <a class="<?php echo activeLink("todo.php") ?>" href="todo.php">Tasks</a>
            </li>

            <li>
                <a class="<?php echo activeLink("admin.php") ?>" href="admin.php">Admin</a>
            </li>
            <li>
                <a class="<?php echo activeLink("profile.php") ?>" href="profile.php">Profile</a>
            </li>
            <li>
                <a href="logout.php">Logout</a>
            </li>


        <?php else:  ?>
            <li>
                <a class="<?php echo activeLink("register.php") ?>" href="register.php">Register</a>
            </li>
            <li>
                <a class="<?php echo activeLink("login.php") ?>" href="login.php">Login</a>
            </li>

        <?php endif; ?>

    </ul>
</nav>
