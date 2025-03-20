<?php
require_once "models/Product.php";
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên

checkLogin(); // Kiểm tra xem người dùng đã đăng nhập hay chưa

$productModel = new Product($conn);
$products = $productModel->getProducts();

// Define $bestSellingProducts
$topRatedProducts = $productModel->getTopRatedProducts();

// Initialize $wishlist as an array
$wishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];

?>

<?php include 'header.php'; ?>

<body>
    <div class="row">
        <div class="col-md-12">

            <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5">
                <?php foreach ($topRatedProducts as $product): ?>
                    <?php
                    $ratingData = $productModel->getProductRating($product["id"]);
                    $averageRating = round($ratingData["average_rating"], 1);
                    $totalReviews = $ratingData["total_reviews"];
                    $isInWishlist = in_array($product["id"], $wishlist);
                    ?>

                    <div class="product-item swiper-slide">
                        <figure>
                            <a href="product_detail.php?id=<?= $product["id"] ?>" title="Product Title">
                                <img src="uploads/product/<?= $product["image"] ?>" alt="Product Thumbnail" class="tab-image">
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
                                        <button class="btn <?= $isInWishlist ? 'btn-danger' : 'btn-outline-dark' ?> rounded-1 p-2 fs-6 btn-heart" data-product-id="<?= $product["id"] ?>">
                                            <svg width="18" height="18">
                                                <use xlink:href="#heart"></use>
                                            </svg>
                                        </button>
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
</body>

<?php include 'flooter.php'; ?>