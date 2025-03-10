<?php
require_once "../models/Product.php";
require_once "../config/database.php"; // Kết nối CSDL

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

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
        $targetDir = "../uploads/"; // Thư mục lưu ảnh
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                if ($productModel->addProduct($name, $price, $description, $fileName, $category_id)) {
                    echo "Thêm sản phẩm thành công! <a href='manage_products.php'>Quay lại</a>";
                } else {
                    echo "Lỗi khi lưu vào database!";
                }
            } else {
                echo "Lỗi khi upload ảnh!";
            }
        } else {
            echo "Chỉ chấp nhận file JPG, JPEG, PNG, GIF!";
        }
    } else {
        echo "Vui lòng chọn ảnh!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm</title>
</head>

<body>
    <h2>Thêm sản phẩm</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Tên sản phẩm" required><br>
        <input type="number" name="price" placeholder="Giá" required><br>
        <textarea name="description" placeholder="Mô tả"></textarea><br>

        <!-- Chọn danh mục -->
        <select name="category_id" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <input type="file" name="image" required><br>
        <button type="submit">Thêm sản phẩm</button>
    </form>
</body>

</html>