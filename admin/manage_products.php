<?php
require_once "../models/Product.php";
require_once "../public/session.php"; // Quản lý phiên
checkAdmin();

$productModel = new Product($conn);
$products = $productModel->getProducts();
?>

<?php include 'header_admin.php'; ?>

<body>
    <div class="pc-container">
        <h2>Danh sách sản phẩm</h2>
        <a href="add_product.php">Thêm sản phẩm</a>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Hình ảnh</th>
                <th>Hành động</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product["id"] ?></td>
                    <td><?= $product["name"] ?></td>
                    <td><?= $product["price"] ?></td>
                    <td><img src="../uploads/<?= $product["image"] ?>" width="50"></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product["id"] ?>">Sửa</a> |
                        <a href="delete_product.php?id=<?= $product["id"] ?>" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

<?php include 'footer_admin.php'; ?>