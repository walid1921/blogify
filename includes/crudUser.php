<?php

require_once "helpers.php";
require_once "database.php";
require_once "session.php";



//! Create new user
function registerUser($pdo, $formData, &$errors, &$successMessage, $autoLogin = false) {

    // 5. Sanitize and validate input
    $username = trim($formData["username"]);
    $email = trim($formData["email"]);
    $password = trim($formData["password"]);
    $confPassword = trim($formData["confPassword"]);
    $age = trim($formData["age"]);
    $phone = filter_var(trim($formData["phone"]),  FILTER_SANITIZE_NUMBER_INT);
    $gender = trim($formData["gender"]);
    $terms = isset($formData["terms"]) ? 1 : 0;


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
    }

    // Gender
    if (empty($gender)) {
        $errors['gender'] = "Gender is required";
    }

    // Terms
    if (!$terms) {
        $errors['terms'] = "You must agree to the terms";
    }


    // 9. Check if there are any errors
    if (!empty($errors)) {
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
        return;
    }

    // 11. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, age, phone, gender, terms) VALUES (:username, :email, :password, :age, :phone, :gender, :terms)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':age' => $age,
            ':phone' => $phone,
            ':gender' => $gender,
            ':terms' => $terms,
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

                    redirect("todo.php");
                } else {
                    $errors['database'] = "User created but could not log in automatically";
                }

            } else {
                redirect("admin.php");
            }
        } else {
            $successMessage = "Registration failed (nothing inserted)";
        }
    } catch (PDOException $e) {
        $errors['database'] = "Registration failed: " . $e->getMessage();
    }
}


//! Delete user
function deleteUser($pdo, $userId) {
    return $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $userId]);

}

//! Edit user
function editUser($pdo, $userId, $newUsername, $newEmail) {
    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
    return $stmt->execute([':username' => $newUsername, ':email' => $newEmail, ':id' => $userId]);
}
