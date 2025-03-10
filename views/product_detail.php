<?php
require_once "../models/Product.php";
require_once "../config/database.php";

if (!isset($_GET["id"])) {
    echo "Sản phẩm không tồn tại!";
    exit();
}

$productModel = new Product($conn);
$product = $productModel->getProductById($_GET["id"]);

if (!$product) {
    echo "Không tìm thấy sản phẩm!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $product["name"] ?></title>
</head>

<body>
    <h2><?= $product["name"] ?></h2>
    <img src="../uploads/<?= $product["image"] ?>" width="300">
    <p>Giá: <?= number_format($product["price"]) ?> VNĐ</p>
    <p><?= $product["description"] ?></p>
    <a href="../index.php">Quay lại trang chủ</a>
    <a href="../views/cart.php?action=add&id=<?= $product["id"] ?>">Thêm vào giỏ hàng</a>
</body>

</html>