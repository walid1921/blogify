<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $pdo;
    private $id;
    private $username;
    private $email;
    private $password;
    private $admin;
    private $age;
    private $phone;
    private $gender;
    private $terms;
    private $created_at;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getAdmin() { return $this->admin; }
    public function getAge() { return $this->age; }
    public function getPhone() { return $this->phone; }
    public function getGender() { return $this->gender; }
    public function getCreatedAt() { return $this->created_at; }

    // Setters
    public function setUsername($username) { $this->username = $username; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setAge($age) { $this->age = $age; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setGender($gender) { $this->gender = $gender; }
    public function setTerms($terms) { $this->terms = $terms; }


    public function getAllUsers() {
        return $this->pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    }


    public function searchUser($search) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username LIKE :search OR email LIKE :search");
        $stmt->execute(['search' => "%$search%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function register($formData, &$errors) {
        // Sanitize and validate input
        $this->setUsername(trim($formData["username"]));
        $this->setEmail(trim($formData["email"]));
        $this->setPassword(trim($formData["password"]));
        $confPassword = trim($formData["confPassword"]);
        $this->setAge(trim($formData["age"]));
        $this->setPhone(filter_var(trim($formData["phone"]), FILTER_SANITIZE_NUMBER_INT));
        $this->setGender(trim($formData["gender"]));
        $this->setTerms(isset($formData["terms"]) ? 1 : 0);

        // Validate input
        if (!$this->validateRegistration($errors, $confPassword)) {
            return false;
        }

        // Check if user exists
        if ($this->userExists()) {
            $errors['username'] = "Username or email already exists";
            return false;
        }

        // Hash password
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        // Insert user
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users 
                (username, email, password, age, phone, gender, terms) 
                VALUES (:username, :email, :password, :age, :phone, :gender, :terms)");

            return $stmt->execute([
                ':username' => $this->username,
                ':email' => $this->email,
                ':password' => $hashedPassword,
                ':age' => $this->age,
                ':phone' => $this->phone,
                ':gender' => $this->gender,
                ':terms' => $this->terms
            ]);
        } catch (PDOException $e) {
            $errors['database'] = "Registration failed: " . $e->getMessage();
            return false;
        }
    }


    public function update($userId, $newUsername, $newEmail) {
        $stmt = $this->pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        return $stmt->execute([
            ':username' => $newUsername,
            ':email' => $newEmail,
            ':id' => $userId
        ]);
    }


    public function delete($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $userId]);
    }


    private function validateRegistration(&$errors, $confPassword) {
        $valid = true;

        // Username validation
        if (empty($this->username) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $this->username)) {
            $errors['username'] = "Username must be 5-20 chars (letters, numbers, underscore)";
            $valid = false;
        }

        // Email validation
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
            $valid = false;
        }

        // Password validation
        if (empty($this->password) || strlen($this->password) < 8 ||
            !preg_match("/[A-Z]/", $this->password) ||
            !preg_match("/[a-z]/", $this->password) ||
            !preg_match("/[0-9]/", $this->password)) {
            $errors['password'] = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, and 1 number.";
            $valid = false;
        }

        // Password confirmation
        if ($this->password !== $confPassword) {
            $errors['confPassword'] = "Passwords do not match";
            $valid = false;
        }

        // Age validation
        if (empty($this->age) || $this->age < 18 || $this->age > 100) {
            $errors['age'] = "Age must be between 18 and 100";
            $valid = false;
        }

        // Phone validation
        if (empty($this->phone) || !preg_match("/^[0-9]{10,15}$/", $this->phone)) {
            $errors['phone'] = "Invalid phone number";
            $valid = false;
        }

        // Gender validation
        if (empty($this->gender)) {
            $errors['gender'] = "Gender is required";
            $valid = false;
        }

        // Terms validation
        if (!$this->terms) {
            $errors['terms'] = "You must agree to the terms";
            $valid = false;
        }

        return $valid;
    }


    private function userExists() {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute([
            'username' => $this->username,
            'email' => $this->email
        ]);
        return $stmt->fetch() !== false;
    }


    public function findById($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $user = new User();
            $user->id = $userData['id'];
            $user->username = $userData['username'];
            $user->email = $userData['email'];
            $user->admin = $userData['admin'];
            $user->age = $userData['age'];
            $user->phone = $userData['phone'];
            $user->gender = $userData['gender'];
            $user->created_at = $userData['created_at'];
            return $user;
        }

        return null;
    }
}
