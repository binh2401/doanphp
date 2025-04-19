<?php
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên
date_default_timezone_set('Asia/Ho_Chi_Minh');
if (!isset($_POST['amount']) || empty($_POST['amount'])) {
    die('Invalid payment amount.');
}

$totalAmount = $_POST['amount'];
$_SESSION["cart_total"] = $totalAmount;

$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://localhost/doanphp/checkout.php"; // URL sau thanh toán
$vnp_TmnCode = "VI1OLR26"; // Mã website
$vnp_HashSecret = "SO86DF6B0ID6FO5E286CM31R7QE7VL43"; // Secret Key

$vnp_TxnRef = uniqid(); // Mã giao dịch
$vnp_OrderInfo = "Thanh toán đơn hàng";
$vnp_OrderType = "billpayment";
$vnp_Amount = $totalAmount * 100; // Nhân 100 vì VNPay yêu cầu đơn vị là đồng
$vnp_Locale = "vn";
$vnp_BankCode = "NCB";
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
$vnp_ExpireDate = date('YmdHis', strtotime('+10 minutes'));

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_ExpireDate" => $vnp_ExpireDate
);

ksort($inputData);
$query = "";
$hashdata = "";
foreach ($inputData as $key => $value) {
    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}
$hashdata = ltrim($hashdata, '&');
$vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
$vnp_Url .= "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;

header('Location: ' . $vnp_Url);
exit();
