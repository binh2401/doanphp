<?php
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên

$vnp_TmnCode = "1YE5VAO8"; // Mã website tại VNPAY
$vnp_HashSecret = "GZOGOKEPMRXPXMUDKLTGEDNEOBAUWIYD"; // Chuỗi bí mật

$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://yourwebsite.com/vnpay_return.php";
$vnp_TxnRef = rand(100000, 999999); // Mã đơn hàng
$vnp_OrderInfo = "Thanh toán đơn hàng";
$vnp_OrderType = "billpayment";
$vnp_Amount = $_POST['amount'] * 100; // Số tiền thanh toán
$vnp_Locale = "vn";
$vnp_BankCode = "";
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

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
);

if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}
ksort($inputData);
$query = "";
$hashdata = "";
foreach ($inputData as $key => $value) {
    $hashdata .= '&' . $key . "=" . $value;
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}
$hashdata = ltrim($hashdata, '&');

$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashdata);
    $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
}

// Check if the URL is valid
$response = @file_get_contents($vnp_Url);
if ($response === FALSE) {
    echo "Không tìm thấy website. Vui lòng kiểm tra lại cấu hình VNPay.";
    exit();
}

header('Location: ' . $vnp_Url);
exit();
