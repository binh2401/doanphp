<?php
require_once "config/database.php";
require_once "models/User.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $image = $_FILES["image"];

    // Xử lý tải lên hình ảnh
    $targetDir = "uploads/user/";
    $targetFile = $targetDir . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Kiểm tra loại tệp
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "Chỉ cho phép các tệp JPG, JPEG, PNG và GIF.";
        exit();
    }

    // Kiểm tra kích thước tệp
    if ($image["size"] > 5000000) {
        echo "Kích thước tệp quá lớn.";
        exit();
    }

    // Lưu tệp
    if (!move_uploaded_file($image["tmp_name"], $targetFile)) {
        echo "Có lỗi xảy ra khi tải lên tệp.";
        exit();
    }

    // Đăng ký người dùng
    $userModel = new User($conn);
    if ($userModel->register($username, $email, $password, $targetFile)) {
        header("Location: login.php");
    } else {
        echo "Có lỗi xảy ra khi đăng ký.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f6f8fa;
        }

        .register-container {
            width: 100%;
            max-width: 360px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .register-container h2 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-control {
            font-size: 14px;
            padding: 8px;
        }

        .btn-register {
            width: 100%;
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        .btn-register:hover {
            background-color: #218838;
        }

        .footer-text {
            margin-top: 10px;
            font-size: 14px;
        }

        .register-containe img {

            height: 80px;
            object-fit: cover;
            margin-bottom: 20px;
            border-radius: 50%;
        }
    </style>
</head>

<body>

    <div class="register-container">
        <img src="uploads/logo.jpg" alt="Logo" class="img-fluid mb-4">
        <h2>Đăng ký tài khoản</h2>
        <form action="register.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-register">Đăng ký</button>
        </form>
        <p class="footer-text">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>

</body>

</html>