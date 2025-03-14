<?php
require_once __DIR__ . "/../config/database.php";

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Hàm đăng ký
    public function register($username, $email, $password, $image)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, image) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword, $image]);
    }

    // Hàm đăng nhập
    public function login($username, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            return [
                "id" => $user["id"],
                "username" => $user["username"],
                "image" => $user["image"],
                "role" => $user["role"]
            ];
        }
        return false;
    }
}
