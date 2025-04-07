<?php
session_start();
require_once "config/database.php";
require_once "public/session.php";

require_once "models/User.php";
require_once "models/Cart.php";
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Không Thành Công</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f8d7da;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #721c24;
        }

        .container {
            text-align: center;
            background-color: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .crying-emoji img {
            width: 150px;
            height: auto;
            animation: cry-animation 2s ease-in-out infinite;
        }

        @keyframes cry-animation {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0);
            }
        }

        h2 {
            font-size: 24px;
            margin: 20px 0;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
        }

        .back-home {
            font-size: 18px;
            text-decoration: none;
            color: #721c24;
            background-color: #f5c6cb;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .back-home:hover {
            background-color: #d9a9b2;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            <div class="crying-emoji">
                <!-- Thay link ảnh dưới đây bằng ảnh nhân vật khóc của bạn -->
                <img src="https://www.example.com/crying-character.png" alt="Crying Character">
            </div>
            <h2>Thanh toán không thành công!</h2>
            <p>Chúng tôi xin lỗi vì sự bất tiện này.</p>
            <a href="index.php" class="back-home">Quay về trang chủ</a>
        </div>
    </div>
</body>

</html>