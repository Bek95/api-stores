<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function create(string $email, string $password): bool
    {
        $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->pdo->prepare($query);

        return $stmt->execute([
            ':email' => $email,
            ':password' => $password,
        ]);
    }

    public function findByEmail(string $email): array
    {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return [];
        }
        return $user;
    }

}