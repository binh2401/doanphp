<?php
require_once "../config/database.php";
require_once "../public/session.php"; // Quản lý phiên
checkAdmin(); // Kiểm tra xem user có phải admin không

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $image = $_FILES["image"];

    if (!empty($name) && !empty($image["name"])) {
        // Xử lý tải lên hình ảnh
        $targetDir = "../uploads/category/";
        $targetFile = $targetDir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Kiểm tra loại tệp
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "<div class='alert alert-danger'>Chỉ cho phép các tệp JPG, JPEG, PNG & GIF.</div>";
            exit;
        }

        // Kiểm tra xem tệp có tồn tại không
        if (file_exists($targetFile)) {
            echo "<div class='alert alert-danger'>Tệp đã tồn tại.</div>";
            exit;
        }

        // Kiểm tra kích thước tệp
        if ($image["size"] > 500000) {
            echo "<div class='alert alert-danger'>Kích thước tệp quá lớn.</div>";
            exit;
        }

        // Di chuyển tệp đã tải lên đến thư mục đích
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            // Lưu đường dẫn hình ảnh vào cơ sở dữ liệu
            $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (:name, :image)");
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":image", $targetFile);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Thêm danh mục thành công! <a href='manage_categories.php'>Quay lại</a></div>";
            } else {
                echo "<div class='alert alert-danger'>Lỗi khi thêm danh mục!</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Lỗi khi tải lên tệp.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Tên danh mục và hình ảnh không được để trống!</div>";
    }
}
?>

<?php include 'header_admin.php'; ?>

<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Thêm danh mục</h2>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Tên danh mục" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm danh mục</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include 'footer_admin.php'; ?>