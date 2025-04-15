<?php


require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";


// 2. Check if user is already logged in
if (isLoggedIn()) {
    redirect("todo.php");
}


// 3. Initialize variables
$username = $email = $password = $confPassword = $age = $phone = $gender = $terms = "";
$usernameErr = $emailErr = $passwordErr = $confPasswordErr = $ageErr = $phoneErr = $genderErr = $termsErr = "";
$successMessage= "";



// 4. Handle form submission
if($_SERVER["REQUEST_METHOD"] === "POST") {


    // 5. Sanitize and validate inputs
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confPassword = trim($_POST["confPassword"]);
    $age = trim($_POST["age"]);
    $phone = trim($_POST["phone"]);
    $gender = trim($_POST["gender"]);
    $terms = isset($_POST["terms"]) ? 1 : 0;

    
    // 6. Username Validation:
    if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
        $usernameErr = "Username must be 5-20 chars (letters, numbers, underscore)";
    }

    // 7. Email Validation:
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }

    // 8. Password validation
    if (empty($password) || strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $passwordErr = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, and 1 number.";
    }

    // Password Confirmation
    if ($password !== $confPassword) {
        $confPasswordErr = "Passwords do not match";
    }


    // Hashing the password
    if (empty($passwordErr) && empty($confPasswordErr)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    // 9. Age Validation
    if (empty($age) || $age < 18 || $age > 100) {
        $ageErr = "Age must be between 18 and 100";
    }

    // 10. Phone Validation
    if (empty($phone) || !preg_match("/^[0-9]{10,15}$/", $phone) || filter_var( $phone,  FILTER_SANITIZE_NUMBER_INT)) {
        $phoneErr = "Invalid phone number (10-15 digits)";
    }

    // 11. Terms and Conditions
    if (!$terms) {
        $termsErr = "You must agree to the terms and conditions";
    }


    
    // 12. Register the user if no errors
    if (!$usernameErr && !$emailErr && !$passwordErr && !$confPasswordErr && !$ageErr && !$phoneErr && !$genderErr && !$termsErr) {


        // 13. Check if username or email already exists
        $stmtCheck = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmtCheck->execute(['username' => $username, 'email' => $email]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        // 14. If username or email exists, show error - If no errors, insert new user into database
        if ($result) {
            $usernameErr = "Username or email already exists";
        } else{
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, age, phone, gender, terms) VALUES (:username, :email, :password, :age, :phone, :gender, :terms)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'age' => $age,
                'phone' => $phone,
                'gender' => $gender,
                'terms' => $terms
            ]);

            // 15. Check if the user was successfully registered, if so, create the session variables for the user and redirect to do list page.
            if ($stmt->rowCount()) {
                $_SESSION["logged_in"] = true;
                $_SESSION["admin"] = false;
                $_SESSION["username"] = $username;
                redirect("todo.php");
            } else {
                $successMessage = "<h3 class='error'> Registration failed (error: " . $stmt->error . ")</h3>";
            }
        }
    }
}

include "./components/header.php";
?>
    
    <div class="hero">
        <div class="form-container">
            <!-- htmlspecialchars() function converts special characters to HTML entities, preventing XSS attacks. -->

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                <h2>Create your Account</h2>

                <input type="text" name="username" placeholder="username" value="<?php echo isset($username) ? $username : "" ?>" required>
                <span class="error"><?php echo $usernameErr; ?></span>

                <input type="email" name="email" placeholder="email" value="<?php echo isset($email) ? $email : "" ?>" required>
                <span class="error"><?php echo $emailErr; ?></span>

                <input type="password" name="password" placeholder="password" required>
                <span class="error"><?php echo $passwordErr; ?></span>

                <input type="password" name="confPassword" placeholder="confirm password" required>
                <span class="error"><?php echo $confPasswordErr; ?></span>


                <input type="number" name="age" placeholder="age"" value="<?php echo isset($age) ? $age : "" ?>" required>
                <span class="error"><?php echo $ageErr; ?></span>

                <input type="text" name="phone" placeholder="phone number" value="<?php echo isset($phone) ? $phone : "" ?>">
                <span class="error"><?php echo $phoneErr; ?></span>

                <div>
                    <input type="radio" name="gender" value="Male"  <?php if ($gender === "Male") {echo "checked";} ?>> Male
                    <input type="radio" name="gender" value="Female"  <?php if ($gender === "Female") {echo "checked";} ?>> Female
                    <input type="radio" name="gender" value="Other"  <?php if ($gender === "Other") {echo "checked";} ?>> Other
                </div>

                <span class="error"><?php echo $genderErr; ?></span>

                <label><input type="checkbox" name="terms" value="agree"> I agree to the terms and conditions</label>
                <span class="error"><?php echo $termsErr; ?></span>

                <p style=font-size:14px>Already have an account? <a href="login.php" >Login</a></p>


                <input type="submit" value="Register">
            </form>
        </div>
    </div>

<?php include "./components/footer.php"; ?>
    
