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
        echo "Đăng ký thành công!";
    } else {
        echo "Có lỗi xảy ra khi đăng ký.";
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
    <form action="register.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Profile Image</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</body>

</html>