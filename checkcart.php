<?php
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên



// Xử lý cập nhật số lượng sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_quantity"])) {
        $productId = $_POST["product_id"];
        $quantity = $_POST["quantity"];
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = max(1, intval($quantity));
        }
    }

    // Xử lý xóa sản phẩm khỏi giỏ hàng
    if (isset($_POST["remove_item"])) {
        $productId = $_POST["product_id"];
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]); // Xóa sản phẩm khỏi giỏ hàng
        }
    }

    // Tải lại trang để cập nhật giỏ hàng
    header("Location:checkcart.php");
    exit();
}

// Tính tổng tiền
$totalAmount = 0;
if (isset($_SESSION['cart'])) {
    $totalAmount = array_sum(array_map(function ($item) {
        return $item['price'] * $item['quantity'];
    }, $_SESSION['cart']));
}
?>

<?php include 'header.php'; ?>

<style>
    .description {
        max-width: 150px;
        /* Adjust the width as needed */
        white-space: normal;
        /* Allow wrapping */
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-details {
        flex: 1;
    }
</style>

<body>
    <div class="container mt-5">
        <h2>Giỏ hàng của bạn</h2>
        <ul class="list-group mb-3">
            <?php if (empty($_SESSION['cart'])): ?>
                <div class='alert alert-warning text-center'>Giỏ hàng của bạn đang trống!</div>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="item-details">
                            <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                            <small class="text-muted description"><?= htmlspecialchars($item['description']) ?></small>
                        </div>
                        <div class="item-details text-center">
                            <span class="text-muted"><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</span>
                        </div>
                        <!-- Form cập nhật số lượng -->
                        <form method="post" class="d-flex">
                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control me-2" min="1">
                            <button type="submit" name="update_quantity" class="btn btn-secondary me-2">Cập nhật</button>
                        </form>

                        <!-- Form xóa sản phẩm -->
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                            <button type="submit" name="remove_item" class="btn btn-danger">Xóa</button>
                        </form>
                    </li>
                <?php endforeach; ?>

                <li class="list-group-item d-flex justify-content-between">
                    <span><strong>Tổng cộng</strong></span>
                    <strong><?= number_format($totalAmount, 0, ',', '.') ?> VNĐ</strong>
                </li>
            <?php endif; ?>
        </ul>

        <a href="vnpay_payment.php" class="w-100 btn btn-primary btn-lg">Thanh toán</a>
        <form action="vnpay_payment.php" method="post">
            <input type="hidden" name="amount" value="<?= $totalAmount ?>">
            <button type="submit" class="w-100 btn btn-success btn-lg mt-2">Thanh toán qua VNPay</button>
        </form>
    </div>
</body>

<?php include 'flooter.php'; ?>