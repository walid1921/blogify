<?php
global $pdo;
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "includes/crudUser.php";

// 2. Check if user is already logged in
if (isLoggedIn()) {
    redirect("todo.php");
}

$errors = [];
$successMessage = "";



// Handle submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    registerUser($pdo, $_POST, $errors, $successMessage, true);
}


include "./components/header.php";
?>

<div class="hero">
    <div class="form-container">
        <!-- htmlspecialchars() function converts special characters to HTML entities, preventing XSS attacks. -->

        <?php if ($successMessage): ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <h2>Create your Account</h2>

            <input type="text" name="username" placeholder="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : "" ?>" required>
            <span class="error"><?php echo isset($errors['username']) ? $errors['username'] : ''; ?></span>

            <input type="email" name="email" placeholder="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>" required>
            <span class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></span>

            <input type="password" name="password" placeholder="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>" required>
            <span class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></span>

            <input type="password" name="confPassword" placeholder="confirm password" required>
            <span class="error"><?php echo isset($errors['confPassword']) ? $errors['confPassword'] : ''; ?></span>

            <input type="number" name="age" placeholder="age" value="<?php echo isset($_POST['age']) ? $_POST['age'] : "" ?>" required>
            <span class="error"><?php echo isset($errors['age']) ? $errors['age'] : ''; ?></span>

            <input type="text" name="phone" placeholder="phone number" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : "" ?>">
            <span class="error"><?php echo isset($errors['phone']) ? $errors['phone'] : ''; ?></span>

            <div>
                <input type="radio" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'checked' : ''; ?>> Male
                <input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'checked' : ''; ?>> Female
                <input type="radio" name="gender" value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'checked' : ''; ?>> Other
            </div>
            <span class="error"><?php echo isset($errors['gender']) ? $errors['gender'] : ''; ?></span>

            <label><input type="checkbox" name="terms" value="agree" <?php echo (isset($_POST['terms']) && $_POST['terms'] === 'agree') ? 'checked' : ''; ?>> I agree to the terms and conditions</label>
            <span class="error"><?php echo isset($errors['terms']) ? $errors['terms'] : ''; ?></span>

            <p style="font-size:14px">Already have an account? <a href="login.php" >Login</a></p>

            <input type="submit" value="Register">
        </form>
    </div>
</div>

<?php include "./components/footer.php"; ?>
