<?php
require_once "../models/Product.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    die("Thiếu ID sản phẩm!");
}

$productModel = new Product($conn);
if ($productModel->deleteProduct($_GET["id"])) {
    header("Location: manage_products.php");
} else {
    echo "Xóa thất bại!";
}
