<?php
require_once "models/Product.php";
require_once "config/database.php";
require_once "public/session.php";

if (!isset($_GET["id"])) {
    echo "Sản phẩm không tồn tại!";
    exit();
}

$productModel = new Product($conn);
$product = $productModel->getProductById($_GET["id"]);

if (!$product) {
    echo "Không tìm thấy sản phẩm!";
    exit();
}

// Xử lý thêm bình luận
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment"])) {
    $comment = trim($_POST["comment"]);
    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (product_id, user_id, comment) VALUES (:product_id, :user_id, :comment)");
        $stmt->bindParam(":product_id", $_GET["id"]);
        $stmt->bindParam(":user_id", $_SESSION["user_id"]);
        $stmt->bindParam(":comment", $comment);
        if ($stmt->execute()) {
            echo "Bình luận của bạn đã được thêm!";
        } else {
            echo "Lỗi khi thêm bình luận!";
        }
    } else {
        echo "Bình luận không được để trống!";
    }
}

// Lấy danh sách bình luận
$stmt = $conn->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE product_id = :product_id ORDER BY created_at DESC");
$stmt->bindParam(":product_id", $_GET["id"]);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="uploads/<?= htmlspecialchars($product["image"]) ?>" class="img-fluid" alt="<?= htmlspecialchars($product["name"]) ?>">
            </div>
            <div class="col-md-6">
                <h2><?= htmlspecialchars($product["name"]) ?></h2>
                <p>Giá: <strong><?= number_format($product["price"]) ?> VNĐ</strong></p>
                <p><?= htmlspecialchars($product["description"]) ?></p>
                <a href="index.php" class="btn btn-secondary">Quay lại trang chủ</a>
                <a href="views/cart.php?action=add&id=<?= $product["id"] ?>" class="btn btn-primary">Thêm vào giỏ hàng</a>
            </div>
        </div>

        <div class="mt-5">
            <h3>Bình luận</h3>
            <?php if (isset($_SESSION["user_id"])): ?>
                <form method="post" class="mb-4">
                    <div class="mb-3">
                        <textarea name="comment" class="form-control" placeholder="Viết bình luận của bạn..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
                </form>
            <?php else: ?>
                <p>Bạn cần <a href="views/login.php">đăng nhập</a> để bình luận.</p>
            <?php endif; ?>

            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="mb-3">
                        <strong><?= htmlspecialchars($comment["username"]) ?></strong> (<?= $comment["created_at"] ?>):
                        <p><?= htmlspecialchars($comment["comment"]) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có bình luận nào.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

<?php include 'flooter.php'; ?>