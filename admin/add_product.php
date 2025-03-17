<?php
require_once "../models/Product.php";
require_once "../config/database.php"; // Kết nối CSDL
require_once "../public/session.php"; // Quản lý phiên
checkAdmin();

$productModel = new Product($conn);

// Lấy danh sách thể loại
$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    $category_id = $_POST["category_id"];

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $targetDir = "../uploads/product/"; // Thư mục lưu ảnh
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                if ($productModel->addProduct($name, $price, $description, $fileName, $category_id)) {
                    echo "<div class='alert alert-success'>Thêm sản phẩm thành công! <a href='manage_products.php'>Quay lại</a></div>";
                } else {
                    echo "<div class='alert alert-danger'>Lỗi khi lưu vào database!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Lỗi khi upload ảnh!</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Chỉ chấp nhận file JPG, JPEG, PNG, GIF!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Vui lòng chọn ảnh!</div>";
    }
}
?>

<?php include 'header_admin.php'; ?>

<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Thêm sản phẩm</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Tên sản phẩm" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá (VNĐ)</label>
                            <input type="number" class="form-control" id="price" name="price" placeholder="Giá" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Mô tả"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include 'footer_admin.php'; ?>