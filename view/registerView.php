<div>
    <canvas id="gradient-canvas"></canvas>

    <div class="hero">
        <div class="form-container">

            <h2>Create your Account</h2>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                <input type="text" name="username" placeholder="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : "" ?>" required>
                <?php if (!empty($this->errors['username'])): ?><span class="error"><?php echo $this->errors['username'] ?></span><?php endif; ?>
                <br>

                <input type="email" name="email" placeholder="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : "" ?>" required>
                <?php if (!empty($this->errors['email'])): ?><span class="error"><?php echo $this->errors['email'] ?></span><?php endif; ?>
                <br>

                <input type="password" name="password" placeholder="password" required>
                <?php if (!empty($this->errors['password'])): ?><span class="error"><?php echo $this->errors['password'] ?></span><?php endif; ?>
                <br>

                <input type="password" name="confPassword" placeholder="confirm password" required>
                <?php if (!empty($this->errors['confPassword'])): ?><span class="error"><?php echo $this->errors['confPassword'] ?></span><?php endif; ?>
                <br>

                <p style="font-size:14px">Already have an account? <a href="login.php" >Login</a></p>

                <input type="submit" value="Register">
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/gradient.js"></script>
