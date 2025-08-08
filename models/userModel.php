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
        return true;
    }

    //! Create user
    public function createUser($data) {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, admin) VALUES (:username, :email, :password, :admin)");
        return $stmt->execute($data);
    }

    //! Get user by id
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //! get All Users
    public function getAllUsers() {
        $stmt = $this->pdo->query("
            SELECT u.id, u.username, u.email, u.created_at, u.admin, COUNT(b.id) AS blogs_num  
            FROM users u LEFT JOIN blogs b ON u.id = b.author_id AND b.is_published = 1
            GROUP BY u.id
            ORDER BY blogs_num DESC;
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //! Search Users by username or email
    public function searchUsers($searchTerm) {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.username, u.email, u.created_at, u.admin, COUNT(b.id) AS blogs_num FROM users u 
            LEFT JOIN blogs b ON u.id = b.author_id AND b.is_published = 1
            WHERE u.username LIKE :search OR u.email LIKE :search
            GROUP BY u.id
            ORDER BY blogs_num DESC;
        ");
        $stmt->execute(['search' => "%$searchTerm%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //! Update User
    public function updateUser($userId, $username, $email) {
        $stmt = $this->pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':id' => $userId
        ]);
    }

    //! Delete User
    public function deleteUser($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $userId]);
    }

    //! Update User Password
    public function updatePassword($userId, $hashedPassword) {
        $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }

}
