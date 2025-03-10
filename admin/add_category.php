<?php
require_once "../config/database.php";
session_start();

// Kiểm tra xem user có phải admin không


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);

    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(":name", $name);
        if ($stmt->execute()) {
            echo "Thêm danh mục thành công! <a href='manage_categories.php'>Quay lại</a>";
        } else {
            echo "Lỗi khi thêm danh mục!";
        }
    } else {
        echo "Tên danh mục không được để trống!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm danh mục</title>
</head>

<body>
    <h2>Thêm danh mục</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Tên danh mục" required><br>
        <button type="submit">Thêm danh mục</button>
    </form>
</body>

</html>