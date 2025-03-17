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
        <div class="pc-content">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mb-4">Danh sách sản phẩm</h2>
                    <a href="add_product.php" class="btn btn-primary mb-3">Thêm sản phẩm</a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Giá</th>
                                    <th>Hình ảnh</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product["id"]) ?></td>
                                        <td><?= htmlspecialchars($product["name"]) ?></td>
                                        <td><?= number_format($product["price"], 2) ?> VNĐ</td>
                                        <td><img src="../uploads/product/<?= htmlspecialchars($product["image"]) ?>" width="50" alt="<?= htmlspecialchars($product["name"]) ?>"></td>
                                        <td>
                                            <a href="edit_product.php?id=<?= htmlspecialchars($product["id"]) ?>" class="btn btn-sm btn-warning">Sửa</a>
                                            <a href="delete_product.php?id=<?= htmlspecialchars($product["id"]) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include 'footer_admin.php'; ?>