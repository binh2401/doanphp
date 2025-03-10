<?php
session_start();
require_once "../models/Product.php";
require_once "../config/database.php";

$productModel = new Product($conn);

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_GET["action"]) && $_GET["action"] == "add" && isset($_GET["id"])) {
    $product = $productModel->getProductById($_GET["id"]);
    if ($product) {
        $cartItem = [
            "id" => $product["id"],
            "name" => $product["name"],
            "image" => $product["image"],
            "price" => $product["price"],
            "quantity" => 1
        ];

        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = [];
        }

        // Kiểm tra sản phẩm đã tồn tại trong giỏ chưa
        $found = false;
        foreach ($_SESSION["cart"] as &$item) {
            if ($item["id"] == $cartItem["id"]) {
                $item["quantity"]++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION["cart"][] = $cartItem;
        }
    }
    header("Location: cart.php");
    exit();
}

// Xóa sản phẩm khỏi giỏ hàng
if (isset($_GET["action"]) && $_GET["action"] == "remove" && isset($_GET["id"])) {
    foreach ($_SESSION["cart"] as $key => $item) {
        if ($item["id"] == $_GET["id"]) {
            unset($_SESSION["cart"][$key]);
            break;
        }
    }
    header("Location: cart.php");
    exit();
}

// Xóa toàn bộ giỏ hàng
if (isset($_GET["action"]) && $_GET["action"] == "clear") {
    unset($_SESSION["cart"]);
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
</head>

<body>
    <h2>Giỏ hàng</h2>
    <?php if (!empty($_SESSION["cart"])): ?>
        <table border="1">
            <tr>
                <th>Tên sản phẩm</th>
                <th>Ảnh</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Hành động</th>
            </tr>
            <?php
            $total = 0;
            foreach ($_SESSION["cart"] as $item):
                $subtotal = $item["price"] * $item["quantity"];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= $item["name"] ?></td>
                    <td><img src="../uploads/<?= $item["image"] ?>" width="100"></td>
                    <td><?= number_format($item["price"]) ?> VNĐ</td>
                    <td><?= $item["quantity"] ?></td>
                    <td><?= number_format($subtotal) ?> VNĐ</td>
                    <td><a href="cart.php?action=remove&id=<?= $item["id"] ?>">Xóa</a></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Tổng cộng</strong></td>
                <td><strong><?= number_format($total) ?> VNĐ</strong></td>
                <td></td>
            </tr>
        </table>
        <a href="cart.php?action=clear">Xóa giỏ hàng</a>
        <a href="checkout.php">Thanh toán</a>
    <?php else: ?>
        <p>Giỏ hàng trống!</p>
    <?php endif; ?>
</body>

</html>