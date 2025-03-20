<?php
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên
require_once "models/Product.php"; // Thêm dòng này để sử dụng lớp Product

checkLogin(); // Kiểm tra xem người dùng đã đăng nhập hay chưa

$productModel = new Product($conn); // Khởi tạo biến $productModel

if (isset($_GET["action"]) && isset($_GET["id"])) {
    $action = $_GET["action"];
    $productId = $_GET["id"];
    $userId = $_SESSION["user_id"];

    if ($action == "add") {
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$userId, $productId]);
        echo "Sản phẩm đã được thêm vào danh sách yêu thích!";
    } elseif ($action == "remove") {
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        echo "Sản phẩm đã được xóa khỏi danh sách yêu thích!";
    } else {
        echo "Hành động không hợp lệ!";
    }
    exit();
}

// Lấy danh sách sản phẩm yêu thích
$wishlist = [];
$stmt = $conn->prepare("SELECT products.* FROM wishlist JOIN products ON wishlist.product_id = products.id WHERE wishlist.user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<body>
    <div class="row">
        <div class="col-md-12">
            <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5">
                <?php foreach ($wishlist as $product): ?>
                    <?php
                    $ratingData = $productModel->getProductRating($product["id"]);
                    $averageRating = round($ratingData["average_rating"], 1);
                    $totalReviews = $ratingData["total_reviews"];
                    ?>
                    <div class="col">
                        <div class="product-item">
                            <figure>
                                <a href="product_detail.php?id=<?= $product["id"] ?>" title="Product Title">
                                    <img src="uploads/product/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="tab-image">
                                </a>
                            </figure>
                            <div class="d-flex flex-column text-center">
                                <h3 class="fs-6 fw-normal"><?= $product["name"] ?></h3>
                                <div>
                                    <span class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <svg width="18" height="18" class="text-warning">
                                                <use xlink:href="#<?= $i <= $averageRating ? 'star-full' : 'star-empty' ?>"></use>
                                            </svg>
                                        <?php endfor; ?>
                                    </span>
                                    <span>(<?= $totalReviews ?> reviews)</span>
                                </div>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <span class="text-dark fw-semibold"><?= number_format($product["price"], 0, ',', '.') ?> VNĐ</span>
                                </div>
                                <div class="button-area p-3 pt-0">
                                    <div class="row g-1 mt-2">
                                        <div class="col-3">
                                            <input type="number" name="quantity" class="form-control border-dark-subtle input-number quantity" value="1">
                                        </div>
                                        <div class="col-7">
                                            <a href="views/cart.php?action=add&id=<?= $product["id"] ?>" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart">
                                                <svg width="18" height="18">
                                                    <use xlink:href="#cart"></use>
                                                </svg> Add to Cart
                                            </a>
                                        </div>
                                        <div class="col-2">
                                            <button class="btn btn-danger rounded-1 p-2 fs-6 btn-heart" data-product-id="<?= $product['id'] ?>">
                                                <svg width="18" height="18">
                                                    <use xlink:href="#heart"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- / product-grid -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heartButtons = document.querySelectorAll('.btn-heart');
            heartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const action = 'remove';
                    // Gửi yêu cầu AJAX để xóa sản phẩm khỏi danh sách yêu thích
                    fetch(`wishlist.php?action=${action}&id=${productId}`)
                        .then(response => response.text())
                        .then(data => {
                            // Thay đổi màu sắc của nút "tym" dựa trên hành động
                            this.classList.toggle('btn-outline-dark');
                            this.classList.toggle('btn-danger');
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</body>

<?php include 'flooter.php'; ?>