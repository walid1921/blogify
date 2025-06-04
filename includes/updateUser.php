<?php
require_once __DIR__ . '/../models/userModel.php';
require_once "session.php";
require_once "helpers.php";

function updateUserService($pdo, $formData, &$errors) {
    $userModel = new UserModel($pdo);

    $userId = isset($formData["userId"]) ? (int)$formData["userId"] : 0;
    $username = trim($formData["username"]);
    $email = trim($formData["email"]);

    // Validate username
    if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
        $errors['username'] = "Username must be 5-20 chars (letters, numbers, underscore)";
    }

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Check for existing username/email
    if (empty($errors)) {
        $existingUser = $userModel->userExists($username, $email);
        if ($existingUser && $existingUser['id'] != $userId) {
            if ($existingUser['username'] === $username) {
                $errors['username'] = "Username already taken.";
            }
            if ($existingUser['email'] === $email) {
                $errors['email'] = "Email already in use.";
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION["message"] = "Please correct the form errors";
        $_SESSION["msg_type"] = "error";
        return false;
    }

    try {
        $success = $userModel->updateUser($userId, $username, $email);

        if ($success) {
            $_SESSION["message"] = "User updated successfully";
            $_SESSION["msg_type"] = "success";
            return true;
        } else {
            $errors['database'] = "Failed to update user";
            $_SESSION["message"] = "Failed to update user";
            $_SESSION["msg_type"] = "error";
            return false;
        }
    } catch (PDOException $e) {
        $errors['database'] = "Database error: " . $e->getMessage();
        $_SESSION["message"] = "An error occurred while updating the user";
        $_SESSION["msg_type"] = "error";
        return false;
    }
}
