<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/userModel.php';
require_once __DIR__ . '/../includes/updateUser.php';
require_once __DIR__ . '/../includes/deleteUser.php';
require_once __DIR__ . '/../includes/updatePassword.php';

class ProfileController {
    private $pdo;
    private $userModel;
    private $errors = [];
    private $user = [];
    private $currentUserId;
    private $currentUser;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
        $this->userModel = new UserModel($this->pdo);
        $this->currentUserId = $_SESSION['user_id'];
        $this->currentUser = strtolower($_SESSION['username']);
    }

    public function handleRequest() {
        $this->user = $this->userModel->getUserById($this->currentUserId);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handlePostRequest();
        }

        $this->showProfile();
    }

    private function handlePostRequest() {
        // Update Profile
        if (isset($_POST["editProfileUser"])) {
            $_POST["userId"] = $this->currentUserId;
            updateUserService($this->pdo, $_POST, $this->errors);
            if (empty($this->errors)) {
                $_SESSION["username"] = $_POST["username"];
                redirect("profile.php");
            }
        }
        // Delete Profile
        elseif (isset($_POST["deleteProfileUser"])) {
            $password = $_POST["password"] ?? '';
            if (deleteUserService($this->pdo, $this->currentUserId, $password, $this->errors)) {
                redirect("logout.php");
            } else {
                redirect("profile.php");
            }
        }
        // Update Password
        elseif (isset($_POST["passwordProfileUser"])) {
            updatePasswordService($this->pdo, $_POST, $this->currentUserId, $this->errors);
        }
    }

    private function showProfile() {
        include __DIR__ . '/../components/header.php';
        include __DIR__ . '/../view/profileView.php';
        include __DIR__ . '/../components/footer.php';
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getUser() {
        return $this->user;
    }

    public function getCurrentUserId() {
        return $this->currentUserId;
    }

    public function getCurrentUser() {
        return $this->currentUser;
    }
}
