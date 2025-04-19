<?php

$host = "localhost";
$dbname = "shop_db";
$username = "root";
$password = ""; // rỗng nếu bạn không đặt mật khẩu cho MySQL

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
