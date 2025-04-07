<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION["cart"])) {
    echo "<div class='alert alert-warning text-center'>Giỏ hàng trống!</div>";
    exit();
}

$userId = $_SESSION["user"]["id"];
$totalPrice = 0;

foreach ($_SESSION["cart"] as $item) {
    $totalPrice += $item["price"] * $item["quantity"];
}

// Lưu đơn hàng vào bảng orders
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_date) VALUES (?, ?, NOW())");
$stmt->execute([$userId, $totalPrice]);
$orderId = $conn->lastInsertId();

// Lưu chi tiết đơn hàng và cập nhật số lượng sản phẩm đã bán
foreach ($_SESSION["cart"] as $item) {
    $stmt = $conn->prepare("INSERT INTO order_detail (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$orderId, $item["id"], $item["quantity"], $item["price"]]);

    // Cập nhật số lượng sản phẩm đã bán
    $stmt = $conn->prepare("UPDATE products SET sales = sales + ? WHERE id = ?");
    $stmt->execute([$item["quantity"], $item["id"]]);
}

// Xóa giỏ hàng sau khi đặt hàng
unset($_SESSION["cart"]);

if (isset($_SESSION['payment_status'])) {
    $status = $_SESSION['payment_status'];
    $message = $_SESSION['payment_message'];

    echo "<div class='alert alert-" . ($status === "success" ? "success" : "danger") . " text-center'>";
    echo htmlspecialchars($message);
    echo "</div>";

    // Clear the payment status from the session
    unset($_SESSION['payment_status']);
    unset($_SESSION['payment_message']);
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow p-4 text-center" style="max-width: 400px;">
        <h2 class="text-success">Đặt hàng thành công!</h2>
        <p class="mt-3">Cảm ơn bạn đã mua hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.</p>
        <a href="index.php" class="btn btn-primary mt-3">Quay lại trang chủ</a>
    </div>
</body>

</html>