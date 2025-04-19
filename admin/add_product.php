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

$errors = []; // Mảng lưu lỗi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $price = trim($_POST["price"]);
    $description = trim($_POST["description"]);
    $category_id = $_POST["category_id"];
    $image = $_FILES["image"];

    // Kiểm tra các trường bắt buộc
    if (empty($name)) {
        $errors['name'] = "Vui lòng nhập tên sản phẩm.";
    }
    if (empty($price)) {
        $errors['price'] = "Vui lòng nhập giá sản phẩm.";
    }
    if (empty($category_id)) {
        $errors['category_id'] = "Vui lòng chọn danh mục.";
    }
    if (empty($image["name"])) {
        $errors['image'] = "Vui lòng chọn hình ảnh.";
    }

    // Nếu không có lỗi, xử lý thêm sản phẩm
    if (empty($errors)) {
        if (isset($image) && $image["error"] == 0) {
            $targetDir = "../uploads/product/"; // Thư mục lưu ảnh
            $fileName = time() . "_" . basename($image["name"]);
            $targetFilePath = $targetDir . $fileName;

            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $allowedTypes = ["jpg", "jpeg", "png", "gif"];

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
                    if ($productModel->addProduct($name, $price, $description, $fileName, $category_id)) {
                        // Chuyển hướng về chính trang này với thông báo thành công
                        header("Location: add_product.php?success=1");
                        exit();
                    } else {
                        echo "<div class='alert alert-danger'>Lỗi khi lưu vào database!</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Lỗi khi upload ảnh!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Chỉ chấp nhận file JPG, JPEG, PNG, GIF!</div>";
            }
        }
    }
}
?>

<?php include 'header_admin.php'; ?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success text-center" role="alert">
        Thêm sản phẩm thành công!
    </div>
<?php endif; ?>

<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Thêm sản phẩm</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Tên sản phẩm" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            <?php if (isset($errors['name'])): ?>
                                <div class="text-danger"><?= $errors['name'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá (VNĐ)</label>
                            <input type="number" class="form-control" id="price" name="price" placeholder="Giá" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                            <?php if (isset($errors['price'])): ?>
                                <div class="text-danger"><?= $errors['price'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Mô tả"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= $category['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['category_id'])): ?>
                                <div class="text-danger"><?= $errors['category_id'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            <img id="preview" src="#" alt="Xem trước hình ảnh" style="display: none; margin-top: 10px; max-width: 100%; height: auto;">
                            <?php if (isset($errors['image'])): ?>
                                <div class="text-danger"><?= $errors['image'] ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Hiển thị hình ảnh
            };

            reader.readAsDataURL(input.files[0]); // Đọc file và chuyển đổi sang URL
        } else {
            preview.src = '#';
            preview.style.display = 'none'; // Ẩn hình ảnh nếu không có file
        }
    }
</script>
<?php include 'footer_admin.php'; ?>