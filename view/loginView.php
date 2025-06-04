<?php include __DIR__ . '/../components/header.php'; ?>

<div>
    <canvas id="gradient-canvas"></canvas>

    <div class="hero <?php echo pageClass(); ?>">
        <div class="form-container">
            <h2>Login to your account</h2>

            <form method="POST" action="">
                <input value="<?php echo htmlspecialchars($username); ?>" type="text" name="username" placeholder="username" required><br>
                <input type="password" name="password" value="<?php echo htmlspecialchars($password); ?>" placeholder="password" required><br>

                <?php if (!empty($error)): ?>
                    <span class="error"><?php echo $error; ?></span>
                <?php endif; ?>

                <p style="font-size:14px">Don't have an account? <a href="register.php">Register</a></p>

                <input type="submit" value="Login">
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/gradient.js"></script>

<?php include __DIR__ . '/../components/footer.php'; ?>
