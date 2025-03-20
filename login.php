<?php
require_once "config/database.php";
require_once "public/session.php";
require_once "models/User.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $userModel = new User($conn);
    $user = $userModel->login($username, $password);

    if ($user) {
        setSession($user); // Sử dụng hàm setSession để lưu thông tin người dùng vào phiên
        header("Location: index.php");
        exit();
    } else {
        $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f8fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }

        .login-container h2 {
            margin-bottom: 20px;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            background-color: #2da44e;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #218739;
        }

        .login-container img {

            height: 80px;
            object-fit: cover;
            margin-bottom: 20px;
            border-radius: 50%;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .bottom-links {
            margin-top: 20px;
            font-size: 14px;
        }

        .bottom-links a {
            color: #0969da;
            text-decoration: none;
        }

        .bottom-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <img src="uploads/logo.jpg" alt="Logo" class="img-fluid mb-4">
        <h2>Đăng nhập</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>
        </form>

        <?php if (isset($error)) : ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="bottom-links">
            <a href="#">Quên mật khẩu?</a>
            <br>
            <a href="register.php">Đăng ký tài khoản mới</a>
        </div>
    </div>

</body>

</html>