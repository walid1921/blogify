<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/registerUser.php';

class RegisterController {

    // Database connection
    private $pdo;
    private $errors = [];

    // Constructor initializes the database connection
    public function __construct() {
        $db = new Database();
        $pdo = $db->getConnection();
        $this->pdo = $pdo;
    }


    // Handles the registration request
    public function handleRequest() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            registerUserService($this->pdo, $_POST, $this->errors, true);
            if (empty($this->errors)) {
                // Successful registration and auto-login redirects inside registerUser()
                // so we don't need to do anything here
                // If you want to redirect to a specific page after registration, you can do it here
                exit;
            }
        }
        $this->showForm();
    }

    // Displays the registration form
    private function showForm() {
        include __DIR__ . '/../components/header.php';
        include __DIR__ . '/../view/registerView.php';
        include __DIR__ . '/../components/footer.php';
    }

    // Returns any errors encountered during registration
    public function getErrors() {
        return $this->errors;
    }
}
