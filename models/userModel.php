<?php

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    //! Check if a username or email already exists
    public function userExists($username, $email, $excludeUserId = null) {
        $sql = "SELECT id, username, email FROM users WHERE (username = :username OR email = :email)";

        if ($excludeUserId) {
            $sql .= " AND id != :excludeId";
        }

        $stmt = $this->pdo->prepare($sql);

        $params = [
            ':username' => $username,
            ':email' => $email
        ];

        if ($excludeUserId) {
            $params[':excludeId'] = $excludeUserId;
        }

        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    //! get All Users with pagination
    public function getAllUsers(?int $limit = null, ?int $offset = null, string $search = ''): array {
        $where= '';
        $params = [];

        if ($search !== '') {
            $where = "WHERE u.username LIKE :search OR u.email LIKE :search";
            $params[':search'] = "%{$search}%";
        }

        $sql = "
            SELECT 
                u.id, u.username, u.email, u.created_at, u.admin, u.is_active,
                (SELECT COUNT(*) FROM blogs b 
                 WHERE b.author_id = u.id AND b.is_published = 1) AS blogs_num
            FROM users u
            $where
            ORDER BY u.admin DESC, blogs_num DESC
        ";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }

        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //! Count Users
    public function countUsers(string $search= ''): int {
        if ($search !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) 
                 FROM users 
                 WHERE username LIKE :search OR email LIKE :search"
            );
            $stmt->execute([':search' => "%{$search}%"]);
        } else {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        }
        return (int)$stmt->fetchColumn();
    }




    // (kept for compatibility; not used anymore – use getAllUsers with $search instead)
    public function searchUsers(string $searchTerm): array {
        return $this->getAllUsers(null, null, $searchTerm);
    }

    //! Update User
    public function updateUser($userId, $username, $email, $is_active) {
        $stmt = $this->pdo->prepare("UPDATE users SET username = :username, email = :email, is_active = :is_active WHERE id = :id");
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':is_active' => $is_active,
            ':id' => $userId,
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
