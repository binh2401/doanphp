<?php
require_once "../models/Product.php";
require_once "../config/database.php";
require_once "../public/session.php"; // Quản lý phiên

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

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product["name"]) ?></title>
</head>

<body>
    <h2><?= htmlspecialchars($product["name"]) ?></h2>
    <img src="../uploads/<?= htmlspecialchars($product["image"]) ?>" width="300">
    <p>Giá: <?= number_format($product["price"]) ?> VNĐ</p>
    <p><?= htmlspecialchars($product["description"]) ?></p>
    <a href="../index.php">Quay lại trang chủ</a>
    <a href="../views/cart.php?action=add&id=<?= $product["id"] ?>">Thêm vào giỏ hàng</a>

    <h3>Bình luận</h3>
    <?php if (isset($_SESSION["user_id"])): ?>
        <form method="post">
            <textarea name="comment" placeholder="Viết bình luận của bạn..." required></textarea><br>
            <button type="submit">Gửi bình luận</button>
        </form>
    <?php else: ?>
        <p>Bạn cần <a href="../views/login.php">đăng nhập</a> để bình luận.</p>
    <?php endif; ?>

    <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
            <div>
                <strong><?= htmlspecialchars($comment["username"]) ?></strong> (<?= $comment["created_at"] ?>):
                <p><?= htmlspecialchars($comment["comment"]) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Chưa có bình luận nào.</p>
    <?php endif; ?>
</body>

</html>