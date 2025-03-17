<?php
require_once "../models/Product.php";
require_once "../config/database.php"; // Kết nối CSDL
require_once "../public/session.php"; // Quản lý phiên
checkAdmin();

$productModel = new Product($conn);

if (!isset($_GET["id"])) {
    die("Thiếu ID sản phẩm!");
}

$product = $productModel->getProductById($_GET["id"]);
if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// Lấy danh sách thể loại
$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    $category_id = $_POST["category_id"];
    $image = $_FILES["image"];

    if ($image["error"] == 0) {
        $targetDir = "../uploads/product/"; // Thư mục lưu ảnh
        $fileName = time() . "_" . basename($image["name"]);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
                $imagePath = $fileName;
            } else {
                echo "<div class='alert alert-danger'>Lỗi khi upload ảnh!</div>";
                $imagePath = $product["image"];
            }
        } else {
            echo "<div class='alert alert-danger'>Chỉ chấp nhận file JPG, JPEG, PNG, GIF!</div>";
            $imagePath = $product["image"];
        }
    } else {
        $imagePath = $product["image"];
    }

    if ($productModel->updateProduct($_GET["id"], $name, $price, $description, $imagePath, $category_id)) {
        echo "<div class='alert alert-success'>Cập nhật sản phẩm thành công! <a href='manage_products.php'>Quay lại</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Cập nhật thất bại!</div>";
    }
}
?>

<?php include 'header_admin.php'; ?>

<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Sửa sản phẩm</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product["name"]) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product["price"]) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($product["description"]) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <img src="<?= htmlspecialchars($product["image"]) ?>" width="100" alt="<?= htmlspecialchars($product["name"]) ?>" class="mt-2">
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include 'footer_admin.php'; ?>