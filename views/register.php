<?php
require_once "../models/User.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userModel = new User($conn);
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if ($userModel->register($username, $email, $password)) {
        echo "Đăng ký thành công! <a href='login.php'>Đăng nhập</a>";
    } else {
        echo "Đăng ký thất bại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
</head>

<body>
    <h2>Đăng ký tài khoản</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Tên đăng nhập" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Mật khẩu" required><br>
        <button type="submit">Đăng ký</button>
    </form>
</body>

</html>