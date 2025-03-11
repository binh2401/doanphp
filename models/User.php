<?php
require_once "../config/database.php";

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Hàm đăng ký
    public function register($username, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    }

    // Hàm đăng nhập
    public function login($username, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            // Start the session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Set session variables
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            // Set cookies (optional, for example, valid for 1 day)
            setcookie("user_id", $user["id"], time() + 86400, "/");
            setcookie("username", $user["username"], time() + 86400, "/");
            setcookie("role", $user["role"], time() + 86400, "/");

            return [
                "id" => $user["id"],
                "username" => $user["username"],
                "role" => $user["role"]
            ];
        }
        return false;
    }
}
