<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION["cart"])) {
    echo "Giỏ hàng trống!";
    exit();
}

$userId = $_SESSION["user"]["id"];
$totalPrice = 0;

foreach ($_SESSION["cart"] as $item) {
    $totalPrice += $item["price"] * $item["quantity"];
}

// Lưu đơn hàng vào bảng orders
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
$stmt->execute([$userId, $totalPrice]);
$orderId = $conn->lastInsertId();

// Lưu chi tiết đơn hàng
foreach ($_SESSION["cart"] as $item) {
    $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$orderId, $item["id"], $item["quantity"], $item["price"]]);
}

// Xóa giỏ hàng sau khi đặt hàng
unset($_SESSION["cart"]);

echo "Đặt hàng thành công!";
echo '<a href="../index.php">Quay lại trang chủ</a>';
