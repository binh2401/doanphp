<?php
session_start();
require_once "config/database.php"; // Include database connection


// Get all VNPay response parameters
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

// Extract the secure hash from the response
$vnp_SecureHash = $_GET['vnp_SecureHash'];
unset($inputData['vnp_SecureHash']);
ksort($inputData);

// Generate the hash to verify the response
$hashData = "";
foreach ($inputData as $key => $value) {
    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
}
$hashData = ltrim($hashData, '&');
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

// Verify the hash and response code
if ($secureHash === $vnp_SecureHash) {
    if ($_GET['vnp_ResponseCode'] == '00') {
        // Payment successful
        $userId = $_SESSION["user"]["id"];
        $totalPrice = $_SESSION["cart_total"]; // Store total price in session during payment initiation

        // Save the order to the database
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_date) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $totalPrice]);
        $orderId = $conn->lastInsertId();

        // Save order details
        foreach ($_SESSION["cart"] as $item) {
            $stmt = $conn->prepare("INSERT INTO order_detail (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $item["id"], $item["quantity"], $item["price"]]);

            // Update product sales
            $stmt = $conn->prepare("UPDATE products SET sales = sales + ? WHERE id = ?");
            $stmt->execute([$item["quantity"], $item["id"]]);
        }

        // Clear the cart
        unset($_SESSION["cart"]);
        unset($_SESSION["cart_total"]);

        // Redirect to checkout page with success message
        $_SESSION['payment_status'] = "success";
        $_SESSION['payment_message'] = "Thanh toán thành công và đơn hàng đã được lưu!";
        header("Location: checkout.php");
        exit();
    } else {
        // Payment failed
        $_SESSION['payment_status'] = "failed";
        $_SESSION['payment_message'] = "Thanh toán không thành công!";
        header("Location: checkout.php");
        exit();
    }
} else {
    // Invalid signature
    $_SESSION['payment_status'] = "error";
    $_SESSION['payment_message'] = "Chữ ký không hợp lệ!";
    header("Location: thanhtoankothanhcong.php");
    exit();
}
