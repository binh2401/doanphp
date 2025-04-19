<?php
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên
require_once 'vendor/autoload.php'; // Composer autoloader
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Kiểm tra email có tồn tại trong CSDL không
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Tạo mật khẩu mới
        $newPassword = substr(md5(time()), 0, 8); // Mật khẩu ngẫu nhiên 8 ký tự
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Cập nhật mật khẩu trong CSDL
        $updateQuery = "UPDATE users SET password = :password WHERE email = :email";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $updateStmt->execute();

        // Gửi email chứa mật khẩu mới
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = ''; // Email của bạn
        $mail->Password = ''; // Mật khẩu email
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('2BNOT@gmail.com', '2BNOT');
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = 'Khôi phục mật khẩu';
        $mail->Body = "Mật khẩu mới của bạn là: <strong>$newPassword</strong>";

        if ($mail->send()) {
            $success = "Mật khẩu mới đã được gửi đến email của bạn.";
        } else {
            $error = "Không thể gửi email. Vui lòng thử lại.";
        }
    } else {
        $error = "Email không tồn tại trong hệ thống.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
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

        .forgot-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }

        .forgot-container h2 {
            margin-bottom: 20px;
        }

        .forgot-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .forgot-container button {
            width: 100%;
            background-color: #2da44e;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .forgot-container button:hover {
            background-color: #218739;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            margin-top: 10px;
        }

        .back-to-login {
            margin-top: 20px;
            display: block;
            text-decoration: none;
            color: #007bff;
        }

        .back-to-login:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="forgot-container">
        <h2>Quên mật khẩu</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Nhập email của bạn" required>
            <button type="submit">Gửi mật khẩu mới</button>
        </form>
        <?php if (isset($error)) : ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (isset($success)) : ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php endif; ?>
        <a href="login.php" class="back-to-login">Quay lại trang đăng nhập</a>
    </div>
</body>

</html>