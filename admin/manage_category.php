<?php
require_once "../config/database.php"; // Kết nối CSDL
require_once "../models/Category.php";
require_once "../public/session.php"; // Quản lý phiên

checkAdmin(); // Kiểm tra xem người dùng có phải là admin hay không

$categoryModel = new Category($conn);
$categories = $categoryModel->getCategories();
?>

<?php include 'header_admin.php'; ?>

<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mb-4">Danh sách thể loại</h2>
                    <a href="add_category.php" class="btn btn-primary mb-3">Thêm thể loại</a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($category["id"]) ?></td>
                                        <td><?= htmlspecialchars($category["name"]) ?></td>
                                        <td>
                                            <a href="edit_category.php?id=<?= htmlspecialchars($category["id"]) ?>" class="btn btn-sm btn-warning">Sửa</a>
                                            <a href="delete_category.php?id=<?= htmlspecialchars($category["id"]) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa thể loại này?')">Xóa</a>
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