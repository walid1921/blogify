<?php
global $pdo;
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "includes/crudUser.php";


if (isLoggedIn()) {
    redirect("todo.php");
}

$errors = [];


// Handle submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    registerUser($pdo, $_POST, $errors, true);
}


include "./components/header.php";
?>

    <div>
        <canvas id="gradient-canvas"></canvas>

        <div class="hero">
            <div class="form-container">

                <h2>Create your Account</h2>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                    <input type="text" name="username" placeholder="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : "" ?>" required>
                    <?php if ($errors['username']): ?><span class="error"><?php echo $errors['username'] ?></span><?php endif; ?>
                    <br>

                    <input type="email" name="email" placeholder="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>" required>
                    <?php if ($errors['email']): ?><span class="error"><?php echo $errors['email'] ?></span><?php endif; ?>
                    <br>


                    <input type="password" name="password" placeholder="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>" required>
                    <?php if ($errors['password']): ?><span class="error"><?php echo $errors['password'] ?></span><?php endif; ?>
                    <br>


                    <input type="password" name="confPassword" placeholder="confirm password" required>
                    <?php if ($errors['confPassword']): ?><span class="error"><?php echo $errors['confPassword'] ?></span><?php endif; ?>
                    <br>


                    <input type="number" name="age" placeholder="age" value="<?php echo isset($_POST['age']) ? $_POST['age'] : "" ?>" required>
                    <?php if ($errors['age']): ?><span class="error"><?php echo $errors['age'] ?></span><?php endif; ?>
                    <br>


                    <input type="text" name="phone" placeholder="phone number" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : "" ?>">
                    <?php if ($errors['phone']): ?><span class="error"><?php echo $errors['phone'] ?></span><?php endif; ?>
                    <br>


                    <div>
                        <input type="radio" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'checked' : ''; ?>> Male
                        <input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'checked' : ''; ?>> Female
                        <input type="radio" name="gender" value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'checked' : ''; ?>> Other
                    </div>
                    <?php if ($errors['gender']): ?><span class="error"><?php echo $errors['gender'] ?></span><?php endif; ?>
                    <br>


                    <label><input type="checkbox" name="terms" value="agree" <?php echo (isset($_POST['terms']) && $_POST['terms'] === 'agree') ? 'checked' : ''; ?>> I agree to the terms and conditions</label>
                    <?php if ($errors['terms']): ?><span class="error"><?php echo  $errors['terms']  ?></span><?php endif; ?>
                    <br>


                    <p style="font-size:14px">Already have an account? <a href="login.php" >Login</a></p>

                    <input type="submit" value="Register">
                </form>
            </div>
        </div>
    </div>

    <script src="/assets/js/gradient.js"></script>

<?php include "./components/footer.php"; ?>
