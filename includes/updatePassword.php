<?php
require_once __DIR__ . '/../models/userModel.php';
require_once "session.php";
require_once "helpers.php";

function updatePasswordService($pdo, $formData, $userId, &$errors) {
    $userModel = new UserModel($pdo);

    $password = isset($formData["password"]) ? $formData["password"] : '';
    $confPassword = isset($formData["confPassword"]) ? $formData["confPassword"] : '';

    // Validate password strength
    if (empty($password) || strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)) {
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
        $user = $userModel->getUserById($userId);

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
        $success = $userModel->updatePassword($userId, $hashedPassword);

        if ($success) {
            $_SESSION["message"] = "Password updated successfully";
            $_SESSION["msg_type"] = "success";
            return true;
        } else {
            $errors['database'] = "Failed to update password";
            $_SESSION["message"] = "Failed to update password";
            $_SESSION["msg_type"] = "error";
            return false;
        }

    } catch (PDOException $e) {
        $errors['database'] = "Database error: " . $e->getMessage();
        $_SESSION["message"] = "An error occurred while updating your password";
        $_SESSION["msg_type"] = "error";
        return false;
    }
}
