<?php
require_once "../models/Product.php";
require_once "../public/session.php"; // Quản lý phiên
checkAdmin();
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$productModel = new Product($conn);

if (!isset($_GET["id"])) {
    die("Thiếu ID sản phẩm!");
}

$product = $productModel->getProductById($_GET["id"]);
if (!$product) {
    die("Sản phẩm không tồn tại!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    $image = $_POST["image"];

    if ($productModel->updateProduct($_GET["id"], $name, $price, $description, $image)) {
        header("Location: manage_products.php");
        exit();
    } else {
        echo "Cập nhật thất bại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
</head>

<body>
    <h2>Sửa sản phẩm</h2>
    <form method="post">
        <input type="text" name="name" value="<?= $product["name"] ?>" required><br>
        <input type="number" name="price" value="<?= $product["price"] ?>" required><br>
        <textarea name="description"><?= $product["description"] ?></textarea><br>
        <input type="text" name="image" value="<?= $product["image"] ?>"><br>
        <button type="submit">Cập nhật</button>
    </form>
</body>

</html>