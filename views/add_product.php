<?php
require_once "../models/Product.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$productModel = new Product($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"];

    // Kiểm tra và upload file
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $targetDir = "../uploads/"; // Thư mục lưu ảnh
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        // Kiểm tra loại file
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Lưu đường dẫn ảnh vào database
                if ($productModel->addProduct($name, $price, $description, $fileName)) {
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
        <input type="file" name="image" required><br>
        <button type="submit">Thêm sản phẩm</button>
    </form>

</body>

</html>