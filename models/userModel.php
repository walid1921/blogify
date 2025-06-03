<?php

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    //! Check if username or email already exists
    public function userExists($username, $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //! Insert user
    public function createUser($data) {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, age, phone, gender, terms, admin) 
            VALUES (:username, :email, :password, :age, :phone, :gender, :terms, :admin)");

        return $stmt->execute($data);
    }

    //! Get user by id
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
