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
    $rating = isset($_POST["rating"]) ? (int)$_POST["rating"] : 0;
    if (!empty($comment) && $rating > 0 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO comments (product_id, user_id, comment, rating) VALUES (:product_id, :user_id, :comment, :rating)");
        $stmt->bindParam(":product_id", $_GET["id"]);
        $stmt->bindParam(":user_id", $_SESSION["user_id"]);
        $stmt->bindParam(":comment", $comment);
        $stmt->bindParam(":rating", $rating);
        if ($stmt->execute()) {
            echo "Bình luận của bạn đã được thêm!";
        } else {
            echo "Lỗi khi thêm bình luận!";
        }
    } else {
        echo "Bình luận và đánh giá sao không được để trống!";
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
    <div class="product-item text-left">
        <div class="row justify-content-center">
            <div class="col-md-6 offset-md-1">
                <img src="uploads/product/<?= htmlspecialchars($product["image"]) ?>" class="img-fluid" alt="<?= htmlspecialchars($product["name"]) ?>">
            </div>
            <div class="col-md-5">
                <h2><?= htmlspecialchars($product["name"]) ?></h2>
                <p>Giá: <strong><?= number_format($product["price"]) ?> VNĐ</strong></p>
                <p class="mt-3">
                    <?= nl2br(htmlspecialchars($product["description"])) ?>
                </p>
                <div class="d-flex justify-content-start mt-3">
                    <a href="index.php" class="btn btn-secondary me-2">Quay lại trang chủ</a>
                    <a href="views/cart.php?action=add&id=<?= $product["id"] ?>" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart">
                        Thêm vào giỏ hàng</a>
                </div>
                <div class="col-3 mt-3">
                    <input type="number" name="quantity" class="form-control border-dark-subtle input-number quantity" value="1">
                </div>
                <h3>Bình luận</h3>
                <?php if (isset($_SESSION["user_id"])): ?>
                    <form method="post" class="mb-4 d-flex flex-column flex-md-row align-items-md-center">
                        <div class="mb-3 flex-grow-1 me-md-3">
                            <textarea name="comment" class="form-control" placeholder="Viết bình luận của bạn..." required></textarea>
                        </div>
                        <div class="mb-3 me-md-3">
                            <label for="rating" class="form-label">Đánh giá:</label>
                            <select name="rating" id="rating" class="form-control" required>
                                <option value="1">1 sao</option>
                                <option value="2">2 sao</option>
                                <option value="3">3 sao</option>
                                <option value="4">4 sao</option>
                                <option value="5">5 sao</option>
                            </select>
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
                            <p>Đánh giá: <?= str_repeat('★', $comment["rating"]) . str_repeat('☆', 5 - $comment["rating"]) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Chưa có bình luận nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

<?php include 'flooter.php'; ?>