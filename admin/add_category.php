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
            echo "Chỉ cho phép các tệp JPG, JPEG, PNG & GIF.";
            exit;
        }

        // Kiểm tra xem tệp có tồn tại không
        if (file_exists($targetFile)) {
            echo "Tệp đã tồn tại.";
            exit;
        }

        // Kiểm tra kích thước tệp
        if ($image["size"] > 500000) {
            echo "Kích thước tệp quá lớn.";
            exit;
        }

        // Di chuyển tệp đã tải lên đến thư mục đích
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            // Lưu đường dẫn hình ảnh vào cơ sở dữ liệu
            $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (:name, :image)");
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":image", $targetFile);
            if ($stmt->execute()) {
                echo "Thêm danh mục thành công! <a href='manage_categories.php'>Quay lại</a>";
            } else {
                echo "Lỗi khi thêm danh mục!";
            }
        } else {
            echo "Lỗi khi tải lên tệp.";
        }
    } else {
        echo "Tên danh mục và hình ảnh không được để trống!";
    }
}
?>

<?php include 'header_admin.php'; ?>

<body>
    <div class="pc-container">
        <h2>Thêm danh mục</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Tên danh mục" required><br>
            <input type="file" name="image" accept="image/*" required><br>
            <button type="submit">Thêm danh mục</button>
        </form>
    </div>
</body>

<?php include 'footer_admin.php'; ?>