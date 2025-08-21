<?php
require_once __DIR__ . '/../models/loginModel.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';

class LoginController {
    private $model;
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
        $this->model = new LoginModel($this->pdo);
    }

    public function handleRequest() {
        $error = '';
        $username = '';
        $password = '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"]);
            $password = trim($_POST["password"]);

            $error = $this->validateLoginInput($username, $password);

            if (!$error) {
                $error = $this->processLogin($username, $password);
            }
        }

        require __DIR__ . '/../view/loginView.php';
    }

    private function validateLoginInput($username, $password) {
        if (empty($username) || empty($password)) {
            return "Username and Password are required.";
        }
        return "";
    }

    private function processLogin($username, $password) {
        $user = $this->model->getUserByUsername($username);

        if ($user["is_active"] === 0) {
            return "This account is not active. Please contact the administrator.";
        }

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true); // Prevent session hijacking
            $_SESSION["logged_in"] = true; // This is how we know the user is logged in
            $_SESSION["username"] = $user["username"]; // this to store the name of the user to be used across pages
            $_SESSION["admin"] = $user["admin"] === 1; // This is how we know the user is an admin
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            redirect('blog.php');
        } else {
            return "Your login credentials are incorrect. Please try again.";
            // here one of the security precautions to not let the hacker knows that the user already exists in the DB or no
        }
    }
}
