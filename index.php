<?php
require_once "models/Product.php";
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên

checkLogin(); // Kiểm tra xem người dùng đã đăng nhập hay chưa


$productModel = new Product($conn);
$products = $productModel->getProducts();
$totalCartAmount = getTotalCartAmount(); // Tính tổng tiền trong giỏ hàng
$stmt = $conn->prepare("SELECT id, name, image FROM categories"); // Thêm 'id' vào truy vấn
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Lấy danh sách sản phẩm yêu thích của người dùng
$userId = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
$stmt->execute([$userId]);
$wishlist = $stmt->fetchAll(PDO::FETCH_COLUMN);

$bestSellingProducts = $productModel->getBestSellingProducts();

// Lấy danh sách sản phẩm có tổng trung bình sao từ cao tới thấp
$topRatedProducts = $productModel->getTopRatedProducts();
?>

<?php include 'header.php'; ?>
<section style="background-image: url('uploads/1.png'); background-repeat: no-repeat; background-size: contain; height: 60vh; display: flex; justify-content: center; align-items: center;     background-position-x: center;">
</section>

<body>


    <section class="py-5 overflow-hidden">
        <div class="container-lg">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-header d-flex flex-wrap justify-content-between mb-5">
                        <h2 class="section-title">Thể loại</h2>
                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-primary me-2">View All</a>
                            <div class="swiper-buttons">
                                <button class="swiper-prev category-carousel-prev btn btn-yellow">❮</button>
                                <button class="swiper-next category-carousel-next btn btn-yellow">❯</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="category-carousel swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($categories as $category): ?>
                                <a href="category.php?id=<?= $category['id'] ?>" class="nav-link swiper-slide text-center">
                                    <img src="uploads/<?= $category['image'] ?>" class="rounded-circle" alt="Category Thumbnail">
                                    <h4 class="fs-6 mt-3 fw-normal category-title"><?= $category['name'] ?></h4>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-5">
        <div class="container-lg">

            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex flex-wrap justify-content-between my-4">

                        <h2 class="section-title">Sản phẩm</h2>

                        <div class="d-flex align-items-center">
                            <a href="all_products.php" class="btn btn-primary rounded-1">View All</a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5">
                        <?php foreach (array_slice($products, 0, 10) as $product): ?>
                            <?php
                            $ratingData = $productModel->getProductRating($product["id"]);
                            $averageRating = round($ratingData["average_rating"], 1);
                            $totalReviews = $ratingData["total_reviews"];
                            $isInWishlist = in_array($product["id"], $wishlist);
                            ?>
                            <div class="col">
                                <div class="product-item">
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
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- / product-grid -->
                </div>
            </div>
        </div>
    </section>


    <section id="featured-products" class="products-carousel">
        <div class="container-lg overflow-hidden py-5">
            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex flex-wrap justify-content-between my-4">

                        <h2 class="section-title">Sản phẩm nổi bật</h2>

                        <div class="d-flex align-items-center">
                            <a href="Outstanding_product.php" class="btn btn-primary me-2">View All</a>
                            <div class="swiper-buttons">
                                <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                                <button class="swiper-next products-carousel-next btn btn-primary">❯</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="swiper">
                        <div class="swiper-wrapper">
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
            </div>
        </div>
    </section>



    <section id="popular-products" class="products-carousel">
        <div class="container-lg overflow-hidden py-5">
            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex justify-content-between my-4">

                        <h2 class="section-title">Sản phẩm bán chạy</h2>

                        <div class="d-flex align-items-center">
                            <a href="best_selling_products.php" class="btn btn-primary me-2">View All</a>
                            <div class="swiper-buttons">
                                <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                                <button class="swiper-next products-carousel-next btn btn-primary">❯</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($bestSellingProducts as $product): ?>
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
            </div>
        </div>
    </section>


    <?php include 'flooter.php'; ?>



</body>

</html>