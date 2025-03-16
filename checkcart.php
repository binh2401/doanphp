<?php
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên

// Kiểm tra xem giỏ hàng có trống không
if (empty($_SESSION['cart'])) {
    echo "Giỏ hàng của bạn đang trống!";
    exit();
}

// Xử lý cập nhật số lượng sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_quantity"])) {
    $productId = $_POST["product_id"];
    $quantity = $_POST["quantity"];
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] = $quantity;
    }
}

$totalAmount = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart']));
?>

<?php include 'header.php'; ?>

<body>
    <div class="container mt-5">
        <h2>Kiểm tra giỏ hàng</h2>
        <ul class="list-group mb-3">
            <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0"><?= $item['name'] ?></h6>
                        <small class="text-body-secondary"><?= $item['description'] ?></small>
                    </div>
                    <span class="text-body-secondary">$<?= $item['price'] ?></span>
                    <form method="post" class="d-flex">
                        <input type="hidden" name="product_id" value="<?= $productId ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control me-2" min="1">
                        <button type="submit" name="update_quantity" class="btn btn-secondary">Cập nhật</button>
                    </form>
                </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between">
                <span>Total (USD)</span>
                <strong>$<?= $totalAmount ?></strong>
            </li>
        </ul>

        <a href="checkout.php" class="w-100 btn btn-primary btn-lg">Proceed to Checkout</a>
    </div>
</body>

<?php include 'flooter.php'; ?>