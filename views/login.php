<?php
require_once "../config/database.php";
require_once "../public/session.php";
require_once "../models/User.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $userModel = new User($conn);
    $user = $userModel->login($username, $password);

    if ($user) {
        setSession($user); // Thiết lập các biến phiên
        header("Location: ../index.php");
        exit();
    } else {
        echo "Tên đăng nhập hoặc mật khẩu không đúng!";
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