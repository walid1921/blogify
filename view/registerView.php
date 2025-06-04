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

                <input type="number" name="age" placeholder="age" value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : "" ?>" required>
                <?php if (!empty($this->errors['age'])): ?><span class="error"><?php echo $this->errors['age'] ?></span><?php endif; ?>
                <br>

                <input type="text" name="phone" placeholder="phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : "" ?>">
                <?php if (!empty($this->errors['phone'])): ?><span class="error"><?php echo $this->errors['phone'] ?></span><?php endif; ?>
                <br>

                <div>
                    <input type="radio" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'checked' : ''; ?>> Male
                    <input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'checked' : ''; ?>> Female
                    <input type="radio" name="gender" value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'checked' : ''; ?>> Other
                </div>
                <?php if (!empty($this->errors['gender'])): ?><span class="error"><?php echo $this->errors['gender'] ?></span><?php endif; ?>
                <br>

                <label><input type="checkbox" name="terms" value="agree" <?php echo (isset($_POST['terms']) && $_POST['terms'] === 'agree') ? 'checked' : ''; ?>> I agree to the terms and conditions</label>
                <?php if (!empty($this->errors['terms'])): ?><span class="error"><?php echo  $this->errors['terms']  ?></span><?php endif; ?>
                <br>

                <p style="font-size:14px">Already have an account? <a href="login.php" >Login</a></p>

                <input type="submit" value="Register">
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/gradient.js"></script>
