<?php
require_once "../models/User.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userModel = new User($conn);
    $username = $_POST["username"];
    $password = $_POST["password"];

    $user = $userModel->login($username, $password);
    if ($user) {
        $_SESSION["user"] = $user;
        header("Location: dashboard.php");
    } else {
        echo "Sai tài khoản hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
</head>

<body>
    <h2>Đăng nhập</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Tên đăng nhập" required><br>
        <input type="password" name="password" placeholder="Mật khẩu" required><br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>

</html>