<?php
require_once __DIR__ . '/../models/userModel.php';
require_once "session.php";
require_once "helpers.php";

function updateUserService($pdo, $formData, &$errors) {
    $userModel = new UserModel($pdo);

    $userId = isset($formData["userId"]) ? (int)$formData["userId"] : 0;
    $username = strtolower(trim($formData["username"]));
    $email = strtolower(trim($formData["email"]));
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Validate username
    if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
        $errors['username'] = "Username must be 5-20 chars (letters, numbers, underscore)";
    }

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // If no validation errors so far, proceed with uniqueness checks
    if (empty($errors)) {
        // Get the current user data
        $currentUser = $userModel->getUserById($userId);
        $currentUserToLower = strtolower($currentUser['username'] ?? '');
        $currentEmailToLower = strtolower($currentUser['email'] ?? '');

        if (!$currentUser) {
            $errors['user'] = "User not found";
        } else {
            // Check username uniqueness only if it changed
            if ($username !== $currentUserToLower) {
                $existingUser = $userModel->userExists($username, $currentEmailToLower, $userId);
                $existingUserUsernameToLower = strtolower($existingUser['username'] ?? '');
                if ($existingUser && $existingUserUsernameToLower === $username) {
                    $errors['username'] = "Username already exists";
                }
            }

            // Check email uniqueness only if it changed
            if ($email !== $currentEmailToLower) {
                $existingUser = $userModel->userExists($currentUserToLower, $email, $userId);
                $existingUserEmailToLower = strtolower($existingUser['email'] ?? '');
                if ($existingUser && $existingUserEmailToLower === $email) {
                    $errors['email'] = "Email already exists";
                }
            }
        }
    }

    // If there are errors, stop and show them
    if (!empty($errors)) {
        $_SESSION["message"] = "Please correct the form errors";
        $_SESSION["msg_type"] = "error";
        return false;
    }

    // Attempt update
    try {
        $success = $userModel->updateUser($userId, $username, $email, $is_active);

        if ($success) {
            $_SESSION["message"] = "User updated successfully";
            $_SESSION["msg_type"] = "success";
            return true;
        } else {
            $errors['database'] = "Failed to update user";
            $_SESSION["message"] = $errors['database'];
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
