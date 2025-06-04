<?php

require_once __DIR__ . '/../models/userModel.php';
require_once "session.php";
require_once "helpers.php";

function registerUserService($pdo, $formData, &$errors, $autoLogin = false, $isAdminPanel = false) {
    $userModel = new UserModel($pdo);

    // Sanitize and validate input
    $username = trim($formData["username"]);
    $email = trim($formData["email"]);
    $password = trim($formData["password"]);
    $confPassword = trim($formData["confPassword"]);
    $age = trim($formData["age"]);
    $phone = filter_var(trim($formData["phone"]), FILTER_SANITIZE_NUMBER_INT);
    $gender = trim($formData["gender"]);
    $terms = isset($formData["terms"]) ? 1 : 0;
    $admin = isset($formData["admin"]) && (string)$formData["admin"] === "1" ? 1 : 0;

    // Username Validation
    if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
        $errors['username'] = "Username must be 5-20 chars (letters, numbers, underscore)";
    }

    // Email Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Username Validation
    if (empty($password) || strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)) {
        $errors['password'] = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, and 1 number.";
    }

    // Confirm Password Validation
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

    // Gender Validation
    if (empty($gender)) {
        $errors['gender'] = "Gender is required";
    }

    // Terms Validation
    if (!$terms) {
        $errors['terms'] = "You must agree to the terms";
    }

    // Admin Validation
    if (!$isAdminPanel && $admin) {
        $errors['admin'] = "You cannot create an admin account";
    }

    // If there are any errors, set the session message (toast) and return
    if (!empty($errors)) {
        $_SESSION["message"] = "Please correct the form errors";
        $_SESSION["msg_type"] = "error";
        return;
    }

    // Check if username/email exists
    if ($userModel->userExists($username, $email)) {
        $errors['username'] = "Username or email already exists";
        $_SESSION["message"] = "Username or email already exists.";
        $_SESSION["msg_type"] = "error";
        return;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create the user
    try {

        $success = $userModel->createUser([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':age' => $age,
            ':phone' => $phone,
            ':gender' => $gender,
            ':terms' => $terms,
            ':admin' => $admin
        ]);

        // If the user was created successfully, set session variables for auto-login
        if ($success) {
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
                $user = $userModel->getUserById($userId);

                if ($user) {
                    session_start();
                    $_SESSION["logged_in"] = true;
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["admin"] = $user["admin"] === 1;
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["message"] = "Welcome to Blogify!";
                    $_SESSION["msg_type"] = "success";
                    redirect("todo.php");
                }  else {
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
            $_SESSION["message"] = "Registration failed";
            $_SESSION["msg_type"] = "error";
        }

    } catch (PDOException $e) {
        $errors['database'] = "Registration failed: " . $e->getMessage();
        $_SESSION["message"] = $errors['database'];
        $_SESSION["msg_type"] = "error";
    }
}
