<?php

global $conn;
include "./components/header.php";

// This the registration page that handles the user registration by validating and storing its data securely in a database.
// Sessions + Form Handling (Validation - Sanitization - Hashing  - Error Handling - Checking if the user exists)


// It begins by checking if a user is already logged-in if so, they are redirected to the app page.
if (isLoggedIn()) {
    redirect("app.php");
}

// isLoggedIn(), redirect() are both custom functions that i've made. custom functions are reusable used to keep the code "Dry"

// Go to the helpers.php

// If not, its has to create a new user using a controlled Form

// I initialized variables to hold user input and error messages coming from the form.
$username = $email = $password = $confPassword = $age = $phone = $gender = $terms = "";
$usernameErr = $emailErr = $passwordErr = $confPasswordErr = $ageErr = $phoneErr = $genderErr = $termsErr = "";
$successMessage= "";



// 2. Here to Check if the request method is POST, for that we use the SuperGlobal $_SERVER["REQUEST_METHOD"]. Because we have two types of requests: GET and POST. The GET method is used to request data from the server, while the POST method is used to send data to a server to create/update a resource. In this case, we are using the POST method to send data to the server for user registration.
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // $_POST = Data is not visible in the URL, will be secure. It is ideal for sensitive data.
    // $_GET = Data is visible to everyone in the URL and its not secure. Data has a limit to send. It can send only strings and it is ideal for non-sensitive data. like Search

    // The mysqli_real_escape_string() to sanitize and ensure that any special characters in the input are escaped, making it safe to use in SQL queries (preventing SQL injection attacks)
    // The trim() function removes whitespace from the beginning and end of a string.
    // The filter_var() function is used to validate and sanitize data. In this case, it is used to sanitize the phone number input by removing any illegal characters. The FILTER_SANITIZE_NUMBER_INT filter removes all characters except digits, plus and minus signs. This ensures that the phone number is stored in a clean format in the database.
    // The isset() function checks if a variable is set and is not NULL. In this case, it checks if the terms checkbox was checked by the user. If it was checked, the $terms variable is set to 1 (true), otherwise it is set to 0 (false).

    $username = mysqli_real_escape_string($conn, $_POST["username"]);

    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = trim($_POST["password"]);
    $confPassword = trim($_POST["confPassword"]);
    $age = mysqli_real_escape_string($conn, $_POST["age"]);
    $phone = filter_var(trim($_POST["phone"]), FILTER_SANITIZE_NUMBER_INT);
    $gender = mysqli_real_escape_string($conn, $_POST["gender"]);
    $terms = isset($_POST["terms"]) ? 1 : 0;
    

    // Username validation:
    // The empty() function checks if the variable is empty ("")
    // The preg_match() is a built-in function checks if the username matches the specified pattern, it takes two arguments: the pattern and the string to check.
    // So it says, If the username is empty or not valid, it sets an error message.

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
    if (!$usernameErr && !$emailErr && !$passwordErr && !$confPasswordErr && !$ageErr && !$phoneErr && !$genderErr && !$termsErr)
{
        


        // Check if username or email already exists, preventing duplicate registrations

        // prepare() prepares an SQL statement for execution, and returns a statement object
        // the SQL query checks if a row exists in the users table where The username, email matches a given value. ? is a placeholder for this value. means SQL query is prepared with placeholders (?)
        $stmtCheck = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        // bind_param() method, does two things: Links the actual values of $username and $email to the placeholders (?), and Defines the data type.
        // SQL injection is prevented because user input is not directly inserted into the query.
        $stmtCheck->bind_param("ss", $username, $email);
        // The execute() method runs the prepared SQL query with the values bound earlier. At this point, the database checks if there are any rows in the users table where: The username matches $username, OR The email matches $email.
        $stmtCheck->execute();
        // The get_result() method to fetch the result from the query. $result now holds the database response, which could be A row or Empty
        $result = $stmtCheck->get_result();
        $stmtCheck->close();  // Close the statement to free up memory and prevent potential issues
    
        if ($result->num_rows > 0) { // num_rows property returns the number of rows in the result set.
            $usernameErr = "Username or email already exists";
        } else{
            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, age, phone, gender, terms) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissi", $username, $email, $hashedPassword, $age, $phone, $gender, $terms);
            
            // Upon successful registration:
            if ($stmt->execute()) {
                session_regenerate_id(true); // Create a new ID for new registration Prevent session hijacking
                $_SESSION["logged_in"] = true; // Set session variable to indicate user is logged in.
                $_SESSION["admin"] = false; // Set session variable to indicate user is not an admin
                $_SESSION["username"] = $username; // Store username in session to be used later
                redirect("app.php");
            } else {
                $successMessage = "<h3 class='error'> Registration failed (error: " . $stmt->error . ")</h3>";
            }
    
            $stmt->close();
        }
    }
    
}

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
    
