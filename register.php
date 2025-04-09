<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";

// This the registration page for new users with (Validation - Sanitization - Hashing  - Error Handling - Checking if the user exists - session "helpers.php" & session security "includes/session.php" ) and storing its data in the database.

// It begins by checking if a user is already logged-in if so, they are redirected to the app page, by using these two customized functions
// (reusable, keep the code dry and readable, their name should be clear) => helpers.php
if (isLoggedIn()) {
    redirect("app.php");
}


// If not (user is not logged-in), its has to create a new user using a controlled Form

// we initialize variables to hold user input and error messages coming from the form. Scroll down to the form
$username = $email = $password = $confPassword = $age = $phone = $gender = $terms = "";
$usernameErr = $emailErr = $passwordErr = $confPasswordErr = $ageErr = $phoneErr = $genderErr = $termsErr = "";
$successMessage= "";



// Here to Check if the request method is POST, for that we use the SuperGlobal $_SERVER["REQUEST_METHOD"]. Because we have two types of requests: GET and POST. The GET method is used to request data from the server, while the POST method is used to send data to a server to create/update a resource. In this case, we are using the POST method to send data to the server for user registration.
if($_SERVER["REQUEST_METHOD"] == "POST") {

    // $_POST is a SuperGlobal array that holds the data submitted through the form. it's connected with the name attribute of the input fields in the form.
    // The trim() function removes whitespace from the beginning and end of a string.
    // The filter_var() function is used to validate and sanitize data. In this case, it is used to sanitize the phone number input by removing any illegal characters. The FILTER_SANITIZE_NUMBER_INT removes all characters except digits, plus and minus signs. This ensures that the phone number is stored in a clean format in the database.
    // The isset() function checks if a variable is set (is clicked) and is not NULL. In this case, it checks if the terms checkbox was checked by the user. If it was checked, the $terms variable is set to 1 (true), otherwise it is set to 0 (false)..

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confPassword = trim($_POST["confPassword"]);
    $age = trim($_POST["age"]);
    $phone = filter_var(trim($_POST["phone"]), FILTER_SANITIZE_NUMBER_INT);
    $gender = trim($_POST["gender"]);
    $terms = isset($_POST["terms"]) ? 1 : 0;

    

    // Username validation:
    // The empty() function checks if the variable is empty ("") // why required attribute not enough, because its not secure enough, so we have to check it in the backend too, because simply you can disable the required attribute in the browser using the developer tools and you will be able to submit the form without filling the username field.
    // The preg_match() is a built-in function checks if the username matches the condition, it takes two arguments: the condition and the string to check.
    // So basically it says, If the username is empty or not valid, it sets an error message.

    if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
        $usernameErr = "Username must be 5-20 chars (letters, numbers, underscore)";
    }

    // Email Validation:
    // The FILTER_SANITIZE_EMAIL filter removes all illegal characters from the email.
    // The FILTER_VALIDATE_EMAIL filter checks if the email is in a valid format.
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }

    // Password validation
    if (empty($password) || strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $passwordErr = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, and 1 number.";
    }

    // Password Confirmation: Ensures the confirmation password matches the original password.
    if ($password !== $confPassword) {
        $confPasswordErr = "Passwords do not match";
    }


    // Hash the password if valid using password_hash() before storing it in the database.
    if (empty($passwordErr) && empty($confPasswordErr)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    // Validate age
    if (empty($age) || $age < 18 || $age > 100) {
        $ageErr = "Age must be between 18 and 100";
    }

    // Validate phone number
    if (empty($phone) || !preg_match("/^[0-9]{10,15}$/", $phone)) {
        $phoneErr = "Invalid phone number (10-15 digits)";
    }

    // Ensure terms are accepted
    if (!$terms) {
        $termsErr = "You must agree to the terms and conditions";
    }


    
    // If all validations pass, proceed with registration
    if (!$usernameErr && !$emailErr && !$passwordErr && !$confPasswordErr && !$ageErr && !$phoneErr && !$genderErr && !$termsErr) {

        // Check if username or email already exists, preventing duplicate registrations

        // we use prepared method in order to securly work with the database and to prevent SQL injection attacks.
        // its basically the same when we used sanitaze inputs to prevent like script injection in the form, this will be to protect the database from SQL injection attacks.
        // for example attackers can inject malicious SQL code into the database query (SQL query to delete database), which can lead to data breaches or unauthorized access.

        // The prepare() method is used to prepare an SQL statement for execution. It takes a SQL query as an argument and returns a statement object.
        // the SQL query checks if a row exists in the users table where The username, email matches a given value. : is a placeholder for this value. means SQL query is prepared with placeholders (:)
        // The execute() method runs the prepared SQL query with the values bound earlier. At this point, the database checks if there are any rows in the users table where: The username matches $username, OR The email matches $email.
        // The fetch method to fetch the result from the query. $result now holds the database response, which could be A row or Empty

        $stmtCheck = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmtCheck->execute(['username' => $username, 'email' => $email]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $usernameErr = "Username or email already exists";
        } else{
            // Insert new user data into database
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

            if ($stmt->rowCount()) {
                $_SESSION["logged_in"] = true;
                $_SESSION["admin"] = false;
                $_SESSION["username"] = $username;
                redirect("app.php");
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
        <!-- htmlspecialchars() function converts special characters to HTML entities, preventing XSS attacks.
        $_SERVER["PHP_SELF"] returns the filename of the currently executing script. This is used to submit the form to the same page for processing. -->

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
            <input type="radio" name="gender" value="Male"  <?php if ($gender === "Male") {
                echo "checked";
            } ?>> Male
            <input type="radio" name="gender" value="Female"  <?php if ($gender === "Female") {
                echo "checked";
            } ?>> Female
            <input type="radio" name="gender" value="Other"  <?php if ($gender === "Other") {
                echo "checked";
            } ?>> Other
            </div>
            <span class="error"><?php echo $genderErr; ?></span>

            <label><input type="checkbox" name="terms" value="agree"> I agree to the terms and conditions</label>
            <span class="error"><?php echo $termsErr; ?></span>

            <input type="submit" value="Register">
        </form>

        </div>
    </div>

<?php include "./components/footer.php"; ?>
    
