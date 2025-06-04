<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/userModel.php';

class UsersTableController {
    private $pdo;
    private $userModel;
    private $errors = [];
    private $users = [];
    private $search = "";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
        $this->userModel = new UserModel($this->pdo);
    }

    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $this->handleGetRequest();
        } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handlePostRequest();
        }

        $this->showUsersTable();
    }

    private function handleGetRequest() {
        $this->search = isset($_GET['search']) ? trim($_GET['search']) : "";

        if (!empty($this->search)) {
            $this->users = $this->userModel->searchUsers($this->search);
        } else {
            $this->users = $this->userModel->getAllUsers();
        }
    }

    private function handlePostRequest() {
        // Register New User
        if (isset($_POST["registerNewUser"])) {
            require_once __DIR__ . '/../includes/registerUser.php';
            registerUserService($this->pdo, $_POST, $this->errors, false, true);
        }
        // Update User
        elseif (isset($_POST["editUser"])) {
            require_once __DIR__ . '/../includes/updateUser.php';
            updateUserService($this->pdo, $_POST, $this->errors);
        }
        // Delete User
        elseif (isset($_POST["deleteUser"])) {
            require_once __DIR__ . '/../includes/deleteUser.php';
            $userId = isset($_POST["userId"]) ? (int)$_POST["userId"] : 0;
            deleteUserService($this->pdo, $userId, $this->errors);
        }

        // Refresh user list after POST operations
        $this->users = $this->userModel->getAllUsers();
    }

    private function showUsersTable() {
        include __DIR__ . '/../components/header.php';
        include __DIR__ . '/../view/usersView.php';
        include __DIR__ . '/../components/footer.php';
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getUsers() {
        return $this->users;
    }

    public function getSearchTerm() {
        return $this->search;
    }
}
