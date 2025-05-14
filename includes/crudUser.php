<?php

require_once "helpers.php";
require_once "database.php";
require_once "session.php";



//! Create new user
function registerUser($pdo, $formData, &$errors, $autoLogin = false, $isAdminPanel = false) {

    // 5. Sanitize and validate input
    $username = trim($formData["username"]);
    $email = trim($formData["email"]);
    $password = trim($formData["password"]);
    $confPassword = trim($formData["confPassword"]);
    $age = trim($formData["age"]);
    $phone = filter_var(trim($formData["phone"]),  FILTER_SANITIZE_NUMBER_INT);
    $gender = trim($formData["gender"]);
    $terms = isset($formData["terms"]) ? 1 : 0;
    $admin = isset($formData["admin"]) && (string)$formData["admin"] === "1" ? 1 : 0;




    // 6. Username Validation
    if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
        $errors['username'] = "Username must be 5-20 chars (letters, numbers, underscore)";
    }

    // 7. Email Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // 8. Password Validation
    if (
        empty($password) || strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {
        $errors['password'] = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, and 1 number.";
    }

    // Password Confirmation
    if ($password !== $confPassword) {
        $errors['confPassword'] = "Passwords do not match";
    }

    // Age Validation
    if (empty($age) || $age < 18 || $age > 100) {
        $errors['age'] = "Age must be between 18 and 100";
    }

    // Phone Validation
    if (empty($phone) || !preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors['phone'] = "Invalid phone number";
        // here
    }

    // Gender
    if (empty($gender)) {
        $errors['gender'] = "Gender is required";
    }

    if(!$isAdminPanel && $admin){
        $errors['admin'] = "You cannot create an admin account";
    }


    // Terms
    if (!$terms) {
        $errors['terms'] = "You must agree to the terms";
    }


    // 9. Check if there are any errors
    if (!empty($errors)) {
        $_SESSION["message"] = "Please correct the form errors";
        $_SESSION["msg_type"] = "error";
        return;
    }

    // 10. Check if username or email already exists
    $stmtCheck = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmtCheck->execute([
        'username' => $username,
        'email' => $email,
    ]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $errors['username'] = "Username or email already exists";
        $_SESSION["message"] = "Username or email already exists.";
        $_SESSION["msg_type"] = "error";
        return;
    }

    // 11. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, age, phone, gender, terms, admin) VALUES (:username, :email, :password, :age, :phone, :gender, :terms, :admin)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':age' => $age,
            ':phone' => $phone,
            ':gender' => $gender,
            ':terms' => $terms,
            ':admin' => $admin
        ]);

        // 12. Check if the user was inserted
        if ($stmt->rowCount()) {
            if ($autoLogin) {


                //  How lastInsertId() Really Works
                //  Connection-Specific: The method returns the ID generated for the current database connection
                //  Each PHP request gets its own dedicated database connection
                //  No other user's registration can interfere during your script execution
                //
                //  Immediate Response: It returns the ID from your most recent insert on this connection
                //  Even if 1000 users register simultaneously, each gets their own correct ID
                //
                //  Atomic Operation: Database inserts are atomic - no two users can get the same ID

                $userId = $pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user){
                    session_start();
                    $_SESSION["logged_in"] = true;
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["admin"] = $user["admin"] === 1; // This is how we know the user is an admin
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION["message"] = "Welcome to Blogify!";
                    $_SESSION["msg_type"] = "success";
                    redirect("todo.php");
                } else {
                    $errors['database'] = "User created but could not log in automatically";
                    $_SESSION["message"] = $errors['database'];
                    $_SESSION["msg_type"] = "error";
                }

            } else {
                $_SESSION["message"] = "User registered successfully";
                $_SESSION["msg_type"] = "success";
                redirect("users.php");
            }
        } else {
            $_SESSION["message"] = "Registration failed: Nothing inserted";
            $_SESSION["msg_type"] = "error";
        }
    } catch (PDOException $e) {
        $errors['database'] = "Registration failed: " . $e->getMessage();
        $_SESSION["message"] = $errors['database'];
        $_SESSION["msg_type"] = "error";
    }
}


//! Delete user
function deleteUser($pdo, $userId, $password = null) {

    // If user is admin, skip password verification
    if (!isAdmin()) {
        // Fetch hashed password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if (!$userRow || !password_verify($password, $userRow['password'])) {
            $_SESSION["message"] = "Incorrect password. Try again";
            $_SESSION["msg_type"] = "error";
            return false;
        }
    }

    try {
        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $_SESSION["message"] = "Account deleted successfully";
        $_SESSION["msg_type"] = "success";
        return true;

    } catch (PDOException $e) {
        $_SESSION["message"] = "An error occurred while deleting your account";
        $_SESSION["msg_type"] = "error";
        return false;
    }
}


//! Edit user
function editUser($pdo, $input, &$errors) {

    $userId = isset($input["userId"]) ? (int)$input["userId"] : (isset($input["id"]) ? (int)$input["id"] : 0);
    $newUsername = isset($input["username"]) ? trim($input["username"]) : '';
    $newEmail = isset($input["email"]) ? trim($input["email"]) : '';

    // Validate username
    if (empty($newUsername) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $newUsername)) {
        $errors['username'] = "Username must be 5-20 chars (letters, numbers, underscore)";
    }

    // Validate email
    if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Check for existing username/email
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = :username OR email = :email) AND id != :id");
        $stmt->execute([
            'username' => $newUsername,
            'email' => $newEmail,
            'id' => $userId
        ]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            if ($existingUser['username'] === $newUsername) {
                $errors['username'] = "Username already taken.";
            }
            if ($existingUser['email'] === $newEmail) {
                $errors['email'] = "Email already in use.";
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION["message"] = "An error occurred while updating your information";
        $_SESSION["msg_type"] = "error";
        return false;
    }

    // Update user information if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
            $stmt->execute([
                ':username' => $newUsername,
                ':email' => $newEmail,
                ':id' => $userId
            ]);

            $_SESSION["message"] = "User information updated successfully";
            $_SESSION["msg_type"] = "success";
            $_SESSION["username"] = $newUsername;
            return true;

        } catch (PDOException $e) {
            $errors['database'] = "Database error: " . $e->getMessage();
            $_SESSION["message"] = "An error occurred while updating your information";
            $_SESSION["msg_type"] = "error";
        }
    }

    return false;
}


//! update password
function updatePassword($pdo, $input, $userId, &$errors) {
    $password = isset($input["password"]) ? $input["password"] : '';
    $confPassword = isset($input["confPassword"]) ? $input["confPassword"] : '';

    // Validate password strength
    if (
        empty($password) ||
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {
        $errors['password'] = "Password must be at least 8 characters and include 1 uppercase letter, 1 lowercase letter, and 1 number.";
    }

    // Validate password confirmation
    if ($password !== $confPassword) {
        $errors['confPassword'] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        return false;
    }

    try {
        // Fetch current user
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors['database'] = "User not found.";
            return false;
        }

        // Prevent reusing old password
        if (password_verify($password, $user['password'])) {
            $errors['password'] = "New password cannot be the same as the current password.";
            return false;
        }

        // Update password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $update->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);

        $_SESSION["message"] = "Password updated successfully. Please log in again";
        $_SESSION["msg_type"] = "success";
        return true;

    } catch (PDOException $e) {
        $errors['database'] = "Database error: " . $e->getMessage();
        $_SESSION["message"] = "An error occurred while updating your password";
        $_SESSION["msg_type"] = "error";
    }

    return false;
}
