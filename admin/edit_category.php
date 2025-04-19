<?php
require_once "../config/database.php";
require_once "../models/category.php";
require_once "../public/session.php"; // Quản lý phiên

checkAdmin(); // Kiểm tra xem người dùng có phải là admin hay không

if (!isset($_GET['id'])) {
    header("Location: manage_category.php");
    exit();
}

$categoryId = $_GET['id'];
$categoryModel = new category($conn);
$category = $categoryModel->getCategoryById($categoryId);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $image = $category['image']; // Giữ nguyên hình ảnh cũ nếu không tải lên hình ảnh mới

    // Kiểm tra nếu có tải lên hình ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../uploads/category/";
        $fileName = md5(time()) . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        // Di chuyển file tải lên vào thư mục đích
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image = "category/" . $fileName; // Cập nhật đường dẫn hình ảnh mới
        }
    }

    // Cập nhật thể loại trong cơ sở dữ liệu
    $categoryModel->updateCategory($categoryId, $name, $image);

    // Chuyển hướng về chính trang này với thông báo thành công
    header("Location: edit_category.php?id=$categoryId&success=1");
    exit();
}
?>

<?php include 'header_admin.php'; ?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success text-center" role="alert">
        Sửa thể loại thành công!
    </div>
<?php endif; ?>

<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mb-4">Sửa thể loại</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Tên thể loại</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="image">Hình ảnh</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <img src="../uploads/<?= htmlspecialchars($category['image']) ?>" alt="Current Image" width="100" class="mt-2">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include 'footer_admin.php'; ?>