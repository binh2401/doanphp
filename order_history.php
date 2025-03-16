<?php
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên
require_once "models/Product.php"; // Thêm dòng này để sử dụng lớp Product

checkLogin(); // Kiểm tra xem người dùng đã đăng nhập hay chưa

$productModel = new Product($conn); // Khởi tạo biến $productModel

// Lấy lịch sử mua hàng của người dùng
$userId = $_SESSION["user_id"];
$stmt = $conn->prepare("
    SELECT o.id AS order_id, o.order_date, od.quantity, od.price, p.name, p.image 
    FROM orders o 
    JOIN order_detail od ON o.id = od.order_id 
    JOIN products p ON od.product_id = p.id 
    WHERE o.user_id = ?
");
$stmt->execute([$userId]);
$orderHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<body>
    <div class="container mt-5">
        <h2>Lịch sử mua hàng</h2>
        <?php if (!empty($orderHistory)): ?>
            <div class="row">
                <?php foreach ($orderHistory as $order): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="uploads/product/<?= htmlspecialchars($order['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($order['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($order['name']) ?></h5>
                                <p class="card-text">Số lượng: <?= $order['quantity'] ?></p>
                                <p class="card-text">Giá: <?= number_format($order['price']) ?> VNĐ</p>
                                <p class="card-text">Ngày đặt hàng: <?= $order['order_date'] ?></p>
                                <a href="product_detail.php?id=<?= $order['order_id'] ?>" class="btn btn-primary">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Bạn chưa có đơn hàng nào.</p>
        <?php endif; ?>
    </div>
</body>

<?php include 'flooter.php'; ?>