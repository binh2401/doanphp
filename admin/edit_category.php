<?php
require_once "../config/database.php";
require_once "../models/Category.php";
require_once "../public/session.php"; // Quản lý phiên

checkAdmin(); // Kiểm tra xem người dùng có phải là admin hay không

if (!isset($_GET['id'])) {
    header("Location: manage_categories.php");
    exit();
}

$categoryId = $_GET['id'];
$categoryModel = new Category($conn);
$category = $categoryModel->getCategoryById($categoryId);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $categoryModel->updateCategory($categoryId, $name);
    header("Location: manage_categories.php");
    exit();
}
?>

<?php include 'header_admin.php'; ?>

<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mb-4">Sửa thể loại</h2>
                    <form method="post">
                        <div class="form-group">
                            <label for="name">Tên thể loại</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include 'footer_admin.php'; ?>