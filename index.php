<?php
require_once "models/Product.php";
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên

checkLogin(); // Kiểm tra xem người dùng đã đăng nhập hay chưa

echo "Xin chào, " . $_SESSION["username"] . "! <a href='views/logout.php'>Đăng xuất</a>";

$productModel = new Product($conn);
$products = $productModel->getProducts();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
</head>

<body>
    <h2>Danh sách sản phẩm</h2>
    <div style="display: flex; flex-wrap: wrap;">
        <?php foreach ($products as $product): ?>
            <div style="border: 1px solid #ddd; padding: 10px; margin: 10px; width: 200px;">
                <img src="uploads/<?= $product["image"] ?>" width="150">
                <h3><?= $product["name"] ?></h3>
                <p>Giá: <?= number_format($product["price"]) ?> VNĐ</p>
                <a href="views/product_detail.php?id=<?= $product["id"] ?>">Xem chi tiết</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>