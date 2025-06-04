<?php
require_once __DIR__ . '/../models/userModel.php';
require_once "session.php";
require_once "helpers.php";

function deleteUserService($pdo, $userId, &$errors, $password = null) {
    $userModel = new UserModel($pdo);

    try {
        // Fetch current user's password
        $user = $userModel->getUserById($userId);

        if (!$user) {
            $errors['database'] = "User not found.";
            return false;
        }

        // Verify password (unless admin is deleting)
        if (!isAdmin() && !password_verify($password, $user['password'])) {
            $errors['deletePassword'] = "Incorrect password. Try again";
            $_SESSION["message"] = "Incorrect password. Try again";
            $_SESSION["msg_type"] = "error";
            return false;
        }

        // Delete user
        $success = $userModel->deleteUser($userId);

        if ($success) {
            $_SESSION["message"] = "Account deleted successfully";
            $_SESSION["msg_type"] = "success";
            return true;
        } else {
            $errors['database'] = "Failed to delete account";
            $_SESSION["message"] = "Failed to delete account";
            $_SESSION["msg_type"] = "error";
            return false;
        }
    } catch (PDOException $e) {
        $errors['database'] = "Database error: " . $e->getMessage();
        $_SESSION["message"] = "An error occurred while deleting your account";
        $_SESSION["msg_type"] = "error";
        return false;
    }
}
