<?php
require_once "../config/database.php";
require_once "../public/session.php"; // Quản lý phiên
checkAdmin(); // Kiểm tra xem user có phải admin không

$errors = []; // Mảng lưu lỗi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $image = $_FILES["image"];

    // Kiểm tra các trường bắt buộc
    if (empty($name)) {
        $errors['name'] = "Vui lòng nhập tên danh mục.";
    }
    if (empty($image["name"])) {
        $errors['image'] = "Vui lòng chọn hình ảnh.";
    }

    if (empty($errors)) {
        // Xử lý tải lên hình ảnh
        $targetDir = "../uploads/category/";
        $targetFile = $targetDir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Kiểm tra loại tệp
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedTypes)) {
            $errors['image'] = "Chỉ cho phép các tệp JPG, JPEG, PNG & GIF.";
        }

        // Kiểm tra xem tệp có tồn tại không
        if (file_exists($targetFile)) {
            $errors['image'] = "Tệp đã tồn tại.";
        }

        // Kiểm tra kích thước tệp
        if ($image["size"] > 500000) {
            $errors['image'] = "Kích thước tệp quá lớn.";
        }

        // Nếu không có lỗi, di chuyển tệp đã tải lên và lưu vào cơ sở dữ liệu
        if (empty($errors)) {
            if (move_uploaded_file($image["tmp_name"], $targetFile)) {
                // Lưu đường dẫn hình ảnh vào cơ sở dữ liệu
                $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (:name, :image)");
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":image", $targetFile);
                if ($stmt->execute()) {
                    // Chuyển hướng về chính trang này với thông báo thành công
                    header("Location: add_category.php?success=1");
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Lỗi khi thêm danh mục!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Lỗi khi tải lên tệp.</div>";
            }
        }
    }
}
?>

<?php include 'header_admin.php'; ?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success text-center" role="alert">
        Thêm danh mục thành công!
    </div>
<?php endif; ?>

<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Thêm danh mục</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Tên danh mục" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            <?php if (isset($errors['name'])): ?>
                                <div class="text-danger"><?= $errors['name'] ?></div>
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
                        <button type="submit" class="btn btn-primary">Thêm danh mục</button>
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